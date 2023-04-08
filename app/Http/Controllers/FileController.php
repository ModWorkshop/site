<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\Mod;
use App\Models\Setting;
use App\Services\APIService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Log;
use Storage;

/**
 * @group Files
 */
class FileController extends Controller
{
    public function __construct() {
        $this->authorizeResource([File::class, 'mod'], "file, mod");

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request, Mod $mod)
    {
        return JsonResource::collection($mod->files()->queryGet($request->val()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Mod $mod)
    {
        $maxSize = Setting::getValue('max_file_size');
        $storageSize = $mod->allowed_storage || Setting::getValue('mod_storage_size');

        $val = $request->validate([
            'file' => "required|max:{$maxSize}"
        ]);

        /**
         * @var UploadedFile $file
         */
        $file = $val['file'];
        $fileSize = $file->getSize();

        $files = $mod->files;
        $accumulatedSize = $fileSize;
        if ($files) {
            foreach ($files as $f) {
                $accumulatedSize += $f->size;   
            }
        }

        if ($accumulatedSize > $storageSize) {
            abort(406, 'Reached maximum allowed storage usage for the mod!');
        }

        $user = $request->user();
        $fileType = $file->extension();
        $fileName = $user->id.'_'.time().'_'.md5(uniqid(rand(), true)).'.'.$fileType;
        $file->storePubliclyAs('mods/files', $fileName);
        
        $file = $mod->files()->create([
            'name' => $file->getClientOriginalName(), //This should be safe to just store in the DB, not the actual stored file name.
            'desc' => '',
            'user_id' => $user->id,
            'file' => $fileName,
            'type' => $fileType,
            'size' => $fileSize
        ]);

        $mod->refresh();
        $mod->bump(false);
        $mod->calculateFileStatus(); //Here it saves

        return $file;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        return $file;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        $val = $request->validate([
            'name' => 'string|min:3|max:100',
            'label' => 'string|nullable|max:100',
            'desc' => 'string|nullable|max:1000',
            'version' => 'string|nullable|max:255',
            'image_id' => 'int|nullable|exists:images,id'
        ]);

        if (isset($val['version']) && $val['version'] !== $file->version) {
            $file->mod->bump();
        }

        APIService::nullToEmptyStr($val, 'label', 'desc', 'version');

        $file->update($val);

        return new FileResource($file);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        $file->delete(); //Deletion of files handled in the model class.
    }


    /**
     * Download File
     * 
     * Begins a download of a file
     *
     * @return void
     */
    public function downloadFile(File $file, Mod $mod=null)
    {
        return Storage::download('mods/files/'.$file->file);
    }

    /**
     * Deletes all files of a mod
     *
     * @param Mod $mod
     * @return void
     */
    public function deleteAllFiles(Mod $mod)
    {
        foreach ($mod->files as $file) {
            $file->delete();
        }

        $mod->calculateFileStatus();
    }
}
