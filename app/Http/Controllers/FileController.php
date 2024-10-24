<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\Mod;
use App\Models\Setting;
use App\Services\APIService;
use App\Services\ModService;
use Arr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use Auth;
use Aws\Exception\AwsException;
use Illuminate\Http\UploadedFile;
use Log;
use Storage;
use Str;

/**
 * @group Files
 */
class FileController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(File::class, Mod::class);
    }

    /**
     * List mod files
     *
     * Returns a list of files of a mod.
     */
    public function index(FilteredRequest $request, Mod $mod)
    {
        return BaseResource::collectionResponse($mod->files()->queryGet($request->val(), function($query, $val) use ($mod) {
            $query->with('user');
            if ($mod->download_type == 'file' && isset($mod->download_id)) {
                $query->orderByRaw("(CASE WHEN id = $mod->download_id THEN 0 ELSE 1 END) ASC, display_order DESC, updated_at DESC");
            } else {
                $query->orderByRaw("display_order DESC, updated_at DESC");
            }
        }));
    }

    public function getFileUploadUrl(Request $request, Mod $mod) {
        $disk = Storage::disk('s3');
        if (!isset($disk)) {
            abort(403);
        }

        $maxSize = Setting::getValue('max_file_size');

        if (isset($mod->user->hasSupporterPerks)) {
            $maxSize = max($maxSize,  Setting::getValue('supporter_mod_storage_size'));
        }

        $val = $request->validate([
            'name' => 'string|min:2|max:100',
            'type' => 'string|min:2|max:50|alpha_dash',
            'length' => "required|int|max:{$maxSize}"
        ]);

        $tempFile = $mod->id.'_'.Auth::user()->id.'_'.Str::random(40).'.'.$val['type'];
        $tempUrl = $disk->temporaryUploadUrl(
            'temp/'.$tempFile, now()->addHours(6),
            [ 'ACL' => 'private' ]
        );

        $file = $mod->files()->create([
            'name' => $val['name'], //This should be safe to just store in the DB, not the actual stored file name.
            'desc' => '',
            'user_id' => $this->user()->id,
            'file' => '',
            'type' => '',
            'size' => $val['length'],
            'temp_file' => $tempFile,
            'temp_waiting' => true
        ]);

        return [
            'id' => $file->id,
            'url' => $tempUrl['url'],
            'headers' => $tempUrl['headers']
        ];
    }

    public function completeUploadViaUploadUrl(File $file) {
        $disk = Storage::disk('s3');

        if (!isset($disk) || !$file->temp_waiting || empty($file->temp_file)) {
            abort(403);
        }

        $file->update([
            'temp_waiting' => false
        ]);

        $mod = $file->mod;

        Log::info('temp/'.$file->temp_file);
        Log::info('mods/files/'.$file->temp_file);

        try {
            // For whatever reason this doesn't work with the normal Storage::move
            $disk->getClient()->copyObject([ 
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'CopySource' => config('filesystems.disks.s3.bucket').'/temp/'.$file->temp_file,
                'Key' => 'mods/files/'.$file->temp_file,
                'ACL' => 'public-read',
            ]);

            $disk->delete('/temp/'.$file->temp_file);

            $file->update([
                'file' => $file->temp_file
            ]);

            $mod->refresh();
            $mod->bump(false);
            $file->refresh();

            return $file;
        } catch (AwsException $e) {
            // Output error message if something goes wrong
            abort(500, "Error copying object: " . $e->getMessage());
        }

        $mod->calculateFileStatus();
    }

    /**
     * Upload a mod file
     *
     * Upload a new file to a mod.
     *
     * @authenticated
     */
    public function store(Request $request, Mod $mod)
    {
        ini_set('memory_limit', '4G');
        set_time_limit(3600);
        $maxSize = Setting::getValue('max_file_size');

        if (isset($mod->user->hasSupporterPerks)) {
            $maxSize = max($maxSize,  Setting::getValue('supporter_mod_storage_size'));
        }

        $val = $request->validate([
            'file' => "required|file|max:{$maxSize}"
        ]);

        [$uploadedFile, $name] = ModService::attemptUpload($mod, $val['file']);

        $file = $mod->files()->create([
            'name' => $uploadedFile->getClientOriginalName(), //This should be safe to just store in the DB, not the actual stored file name.
            'desc' => '',
            'user_id' => $this->user()->id,
            'file' => $name,
            'type' => $uploadedFile->getClientOriginalExtension(),
            'size' => $uploadedFile->getSize()
        ]);

        $mod->refresh();
        $mod->bump(false);
        $mod->calculateFileStatus(); //Here it saves

        return response($file, 201);
    }

    /**
     * Get a file
     *
     * Returns a file. If the mod it belongs to is accessible.
     */
    public function show(File $file)
    {
        return $file;
    }

    /**
     * Update a file
     *
     * @authenticated
     */
    public function update(Request $request, File $file)
    {
        ini_set('memory_limit', '4G');
        set_time_limit(3600);

        $maxSize = Setting::getValue('max_file_size');

        if (isset($file->mod->user->hasSupporterPerks)) {
            $maxSize = max($maxSize,  Setting::getValue('supporter_mod_storage_size'));
        }

        $val = $request->validate([
            'name' => 'string|min:2|max:100',
            'label' => 'string|nullable|max:100',
            'desc' => 'string|nullable|max:1000',
            'version' => 'string|nullable|max:255',
            'display_order' => 'integer|min:-1000|max:1000|nullable',
            'image_id' => 'int|nullable|exists:images,id',
            'change_file' => "nullable|file|max:{$maxSize}"
        ]);

        APIService::nullToEmptyStr($val, 'label', 'desc', 'version');

        if ((isset($val['version']) && $val['version'] !== $file->version)) {
            $file->mod->bump();
        }

        if (isset($val['change_file'])) {
            [$uploadFile, $name] = ModService::attemptUpload($file->mod, Arr::pull($val, 'change_file'));

            //Delete old file
            Storage::delete('mods/files/'.$file->file);

            $val['file'] = $name;
            $val['type'] = $uploadFile->getType();
            $val['size'] = $uploadFile->getSize();

            $file->mod->bump();
        }

        $file->update($val);

        return new FileResource($file);
    }

    /**
     * Delete a file
     *
     * @authenticated
     */
    public function destroy(File $file)
    {
        $file->delete(); //Deletion of files handled in the model class.
    }

    /**
     * Download a file
     *
     * Begins a download of a file
     */
    public function downloadFile(File $file, Mod $mod=null)
    {
        return redirect($file->downloadUrl);
    }

    /**
     * Get file version
     *
     * Returns the version set for the file
     */
    public function fileVersion(File $file, Mod $mod=null)
    {
        return $file->version;
    }

    /**
     * Delete all mod files
     *
     * @authenticated
     */
    public function deleteAllFiles(Mod $mod)
    {
        foreach ($mod->files as $file) {
            $file->delete();
        }

        $mod->calculateFileStatus();
    }

    /**
     * List file versions
     *
     * Returns a list of versions (Up to 100 mods)
     * Convenient way of getting many versions at once and avoid sending too many requests
     * Warning: this bypasses any mod visibility/suspension, however the only information it returns are verisons, nothing else.
     * You cannot use this to download these files or figure out what mod they belongs to.
     */
    public function getVersions(Request $request) {
        $val = $request->validate([
            'file_ids' => 'array|required',
            'file_ids.*' => 'integer|min:1',
        ]);

        $files = File::whereIn('id', $val['file_ids']);
        $onlyVersions = [];
        foreach($files as $file) {
            $onlyVersions[$file->id] = $file->version;
        }

        return $onlyVersions;
    }

    /**
     * Register download
     *
     * Registers a download for the file, doesn't let you 'download' it twice
     * It applies the download to the mod that the file belongs to.
     * Works with guests
     */
    public function registerDownload(Request $request, File $file)
    {
        ModService::registerDownload($file);
    }
}
