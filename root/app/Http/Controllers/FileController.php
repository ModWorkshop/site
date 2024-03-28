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
                $query->orderByRaw("(CASE WHEN id = $mod->download_id THEN 0 ELSE 1 END) ASC, updated_at DESC");
            } else {
                $query->orderByDesc("updated_at");
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

        if (isset($mod->user->activeSupporter)) {
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

        if (isset($file->mod->user->activeSupporter)) {
            $maxSize = max($maxSize,  Setting::getValue('supporter_mod_storage_size'));
        }

        $val = $request->validate([
            'name' => 'string|min:3|max:100',
            'label' => 'string|nullable|max:100',
            'desc' => 'string|nullable|max:1000',
            'version' => 'string|nullable|max:255',
            'image_id' => 'int|nullable|exists:images,id',
            'change_file' => "nullable|file|max:{$maxSize}"
        ]);

        APIService::nullToEmptyStr($val, 'label', 'desc', 'version');

        if (isset($val['version']) && $val['version'] !== $file->version) {
            $file->mod->bump();
        }

        if (isset($val['change_file'])) {
            [$uploadFile, $name] = ModService::attemptUpload($file->mod, Arr::pull($val, 'change_file'));

            //Delete old file
            Storage::delete('mods/files/'.$file->file);

            $val['file'] = $name;
            $val['type'] = $uploadFile->getType();
            $val['size'] = $uploadFile->getSize();
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
}
