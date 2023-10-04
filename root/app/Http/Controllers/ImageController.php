<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Image;
use App\Models\Mod;
use App\Models\Setting;
use App\Services\APIService;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\File;

/**
 * @group Mods
 *
 * @subgroup Images
 */
class ImageController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Image::class, Mod::class);
    }
    /**
     * Get List of Mod Images
     *
     * Returns images of the mod
     */
    public function index(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'limit' => 'integer|min:1|max:50'
        ]);

        return BaseResource::collectionResponse($mod->images()->queryGet($val));
    }

    /**
     * Upload Image
     *
     * Upload a new image to the mod
     */
    public function store(Request $request, Mod $mod)
    {
        set_time_limit(1800);

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

        return Image::create([
            'user_id' => $this->userId(),
            'mod_id' => $mod->id,
            'file' => $name,
            'has_thumb' => true,
            'type' => $type,
            'size' => $size
        ]);
    }

    /**
     * Get Image
     *
     * Returns data about a single image
     */
    public function show(Image $image)
    {
        return $image;
    }

    /**
     *  @hideFromAPIDocumentation
     */
    public function update(Request $request, Mod $mod, Image $image)
    {
        //TODO
    }

    /**
     * Delete an Image
     *
     * @authenticated
     */
    public function destroy(Image $image)
    {
        $image->delete(); //Deletion of files handled in the model class.
    }

    /**
     * Delete all Mod Images
     *
     * @authenticated
     */
    public function deleteAllImages(Mod $mod)
    {
        foreach ($mod->images as $image) {
            $image->delete();
        }
    }
}
