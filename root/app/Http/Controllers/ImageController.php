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
use Illuminate\Support\Collection;
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
            'limit' => 'integer|min:1|max:50',
            'only_visible' => 'boolean|nullable'
        ]);

        return BaseResource::collectionResponse($mod->images()->queryGet($val, function($q, $val) {
            if ($val['only_visible']) {
                $q->where('visible', true);
            }
        }));
    }

    /**
     * Upload Image
     *
     * Upload a new image to the mod
     */
    public function store(Request $request, Mod $mod)
    {
        set_time_limit(1800);

        $count = $mod->images()->count();

        if ($count >= Setting::getValue('mod_max_image_count')) {
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
            'display_order' => $count,
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
    public function update(Request $request, Image $image)
    {
        $val = $request->validate([
            'display_order' => 'integer|min:-1000|max:1000|nullable',
            'visible' => 'boolean|nullable'
        ]);

        if (isset($val['display_order'])) {
            /**
             * @var Collection
             */
            $images = $image->mod->images()->orderBy('display_order')->where('id', '!=', $image->id)->get();
            $images->splice($val['display_order'], 0, [$image]);
            for ($i=0; $i < $images->count(); $i++) {
                $img = $images[$i];
                $img->display_order = $i;
                $img->save();
            }
        }

        if (isset($val['visible'])) {
            $image->update([
                'visible' => $val['visible']
            ]);
        }

        return $image;
    }

    /**
     * Delete an Image
     *
     * @authenticated
     */
    public function destroy(Image $image)
    {
        $mod = $image->mod;
        $image->delete(); //Deletion of files handled in the model class.

        $images = $mod->images()->orderBy('display_order')->get();
        for ($i=0; $i < count($images); $i++) {
            $images[$i]->display_order = $i;
            $images[$i]->save();
        }
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
