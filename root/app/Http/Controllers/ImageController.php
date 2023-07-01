<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Image;
use App\Models\Mod;
use App\Models\Setting;
use App\Services\APIService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\File;

class ImageController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Image::class, Mod::class);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'limit' => 'integer|min:1|max:50'
        ]);

        return JsonResource::collection($mod->images()->queryGet($val));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Mod $mod)
    {
        if ($mod->images()->count() >= Setting::getValue('mod_max_image_count')) {
            abort(406, 'Reached maximum allowed images for the mod!');
        }

        $val = $request->validate([
            'file' => ['file', 'required', File::image()->max(Setting::getValue('image_max_file_size') / 1024)]
        ]);

        /** @var UploadedFile $file */
        $file = $val['file'];

        [
            'name' => $name,
            'type' => $type,
            'size' => $size
        ] = APIService::storeImage($file, 'mods/images', null, 300);

        $img = Image::create([
            'user_id' => $this->userId(),
            'mod_id' => $mod->id,
            'file' => $name,
            'has_thumb' => true,
            'type' => $type,
            'size' => $size
        ]);

        return $img;
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        return $image;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mod $mod, Image $image)
    {
        //TODO
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        $image->delete(); //Deletion of files handled in the model class.
    }

    /**
     * Deletes all files of a mod
     */
    public function deleteAllFiles(Mod $mod)
    {
        foreach ($mod->files as $file) {
            $file->delete();
        }

        $mod->calculateFileStatus();
    }

    /**
     * Deletes all images of a mod
     */
    public function deleteAllImages(Mod $mod)
    {
        foreach ($mod->images as $image) {
            $image->delete();
        }
    }
}
