<?php

namespace App\Http\Controllers;

use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\Mod;
use Illuminate\Http\Request;
use Storage;

/**
 * @group Files
 */
class FileController extends Controller
{
    public function __construct() {
        $this->authorizeResource(File::class, 'file');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'file' => 'required|max:512000|mimes:zip,rar,7z'
        ]);

        $user = $request->user();
        /**
         * @var UploadedFile $file
         */
        $file = $val['file'];
        $fileType = $file->extension();
        $fileName = $user->id.'_'.time().'_'.md5(uniqid(rand(), true)).'.'.$fileType;
        $file->storePubliclyAs('mods/files', $fileName);
        
        $file = File::create([
            'name' => $file->getClientOriginalName(), //This should be safe to just store in the DB, not the actual stored file name.
            'desc' => '',
            'user_id' => $user->id,
            'mod_id' => $mod->id,
            'file' => $fileName,
            'type' => $fileType,
            'size' => $file->getSize()
        ]);

        $mod->save();

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
    public function update(Request $request, Mod $mod, File $file)
    {
        $val = $request->validate([
            'name' => 'string|min:3|max:100',
            'label' => 'string|nullable|max:100',
            'desc' => 'string|nullable|max:1000',
            'version' => 'string|nullable|max:255'
        ]);

        $val['label'] ??= '';
        $val['desc'] ??= '';
        $val['version'] ??= '';

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
    public function downloadFile(File $file)
    {
        return Storage::download('mods/files/'.$file->file);
    }
}
