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
use Illuminate\Http\UploadedFile;
use Log;
use Storage;

/**
 * @group Mods
 *
 * @subgroup Files
 */
class FileController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(File::class, Mod::class);
    }

    /**
     * List of Mod Files
     *
     * Returns a list of files of a mod.
     */
    public function index(FilteredRequest $request, Mod $mod)
    {
        return BaseResource::collectionResponse($mod->files()->queryGet($request->val(), function($query, $val) use ($mod) {
            if ($mod->download_type == 'file' && isset($mod->download_id)) {
                $query->orderByRaw("(CASE WHEN id = $mod->download_id THEN 0 ELSE 1 END) ASC, display_order DESC, updated_at DESC");
            } else {
                $query->orderByRaw("display_order DESC, updated_at DESC");
            }
        }));
    }

    /**
     * Upload Mod File
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

        return $file;
    }

    /**
     * Get File
     *
     * Returns a file. If the mod it belongs to is accessible.
     */
    public function show(File $file)
    {
        return $file;
    }

    /**
     * Update File
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
     * Delete File
     *
     * @authenticated
     */
    public function destroy(File $file)
    {
        $file->delete(); //Deletion of files handled in the model class.
    }

    /**
     * Download File
     *
     * Begins a download of a file
     */
    public function downloadFile(File $file, Mod $mod=null)
    {
        return redirect($file->downloadUrl);
    }

    /**
     * Get File Version
     *
     * Returns the version set for the file
     */
    public function fileVersion(File $file, Mod $mod=null)
    {
        return $file->version;
    }

    /**
     * Delete All Mod FIles
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
     * Get File Versions
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
     * Register Download
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
