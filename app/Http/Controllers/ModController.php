<?php

namespace App\Http\Controllers;

use App\Http\Resources\ModResource;
use App\Models\Image;
use App\Models\Mod;
use Arr;
use File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ModController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Mod::class, 'mod');
    }

    /**
     * Mods
     * 
     * Returns many mods, has a few options for searching the right mods
     *
     * @param Request $request
     * return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Query parameters
        $val = $request->validate([
            // How many mods should this return. 
            'limit' => 'integer|min:1|max:50',
            'tags' => 'array',
            'tags.*' => 'integer|min:1',
            'notInTags' => 'array',
            // Filter out mods that are in these tags
            'notInTags.*' => 'integer|min:1'
        ]);
        
        $query = Mod::limit($val['limit'] ?? 40)->list()->with(['submitter' => fn($q) => $q->withPermissions()])->orderBy('updated_at', 'DESC');
        
        if (isset($val['tags'])) {
            $query->whereHasIn('tags', function(Builder $q) use ($val) {
                $q->limit(1)->whereIn('tags.id', $val['tags']);
            });
        }
        
        if (isset($val['notInTags'])) {
            $query->whereDoesntHaveIn('tags', function(Builder $q) use ($val) {
                $q->limit(1)->whereIn('tags.id', $val['notInTags']);
            });
        }

        return $query->get();
    }

    /**
     * Create Mod
     * 
     * Creates a new mod
     * 
     * Please refer to the edit of mods for parameters
     * 
     * @authenticated
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request);
    }

    /**
     * Mod
     * 
     * Returns a single mod
     * 
     * @urlParam mod integer required The ID of the mod
     *
     * @param Mod $mod
     * @return \Illuminate\Http\Response
     */
    public function show(Mod $mod)
    {
        return new ModResource($mod);
    }

    /**
     * Update Mod
     * 
     * Updates data of a mod
     * 
     * @authenticated
     * 
     * @param Request $request
     * @param Mod|null $mod
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mod $mod=null)
    {
        $val = $request->validate([
            'name' => 'string|min:3|max:150',
            'desc' => 'string|min:3|max:30000',
            'license' => 'string|nullable|max:30000',
            'changelog' => 'string|nullable|max:30000',
            'short_desc' => 'string|nullable|max:150',
            'donation' => 'string|nullable|max:100',
            'version' => 'string|nullable|max:100',
            'visibility' => 'integer|min:1|max:4',
            'game_id' => 'integer|min:1|exists:categories,id',
            'category_id' => 'integer|min:1|nullable|exists:categories,id',
            'tag_ids' => 'array',
            'tag_ids.*' => 'integer|min:1',
            'download_id' => 'integer|min:1|nullable',
            'download_type' => 'string|nullable|in:file,link'
        ]);

        if (isset($mod) && array_key_exists('download_id', $val)) {
            $downloadId = Arr::pull($val, 'download_id');
            if (isset($downloadId)) {
                $type = Arr::pull($val, 'download_type');
                $download = null;
                if ($type == 'file') {
                    $download = $mod->files->find($downloadId);
                } else if($type == 'link') {
    
                }
    
                if (isset($download)) {
                    $mod->download()->associate($download);
                } else {
                    throw ValidationException::withMessages(['download_id' => "The download doesn't exist in the mod"]);
                }
            } else {
                $mod->download()->dissociate();
            }
        }

        $tags = Arr::pull($val, 'tag_ids'); // Since 'tags' isn't really inside the model, we need to pull it out.

        if (isset($mod)) {
            $mod->update($val);
        } else {
            // Never put something like $request->all(); in create.
            //Laravel may have guard for this, but there's really no reason what to so ever to give it that.
            $val['submitter_id'] = $request->user()->id;
            $mod = Mod::create($val); // Validate handles the important stuff already.
        }

        if(isset($tags)) {
            $mod->tags()->sync($tags);
        }

        return new ModResource($mod);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Upload mod image
     * 
     * @param Request $request
     * @param Mod $mod
     * @return void
     */
    public function uploadModImage(Request $request, Mod $mod) {
        $val = $request->validate([
            'file' => 'required|max:512000|mimes:png,webp,gif'
        ]);

        $user = $request->user();
        /**
         * @var UploadedFile $file
         */
        $file = $val['file'];
        $fileType = $file->extension();
        $fileName = $user->id.'_'.time().'_'.md5(uniqid(rand(), true)).'.'.$fileType;
        $file->storePubliclyAs('images', $fileName, 'public');
        
        $img = Image::create([
            'user_id' => $user->id,
            'mod_id' => $mod->id,
            'file' => $fileName,
            'type' => $fileType,
            'size' => $file->getSize()
        ]);

        return $img;
    }

    /**
     * Delete Image
     * 
     * Deletes an image from a mod
     *
     * @param Mod $mod
     * @param Image $img
     * @return void
     */
    public function deleteModImage(Request $request, Mod $mod, Image $image)
    {
        $image->delete(); //Deletion of files handled in the model class.
    }


    /**
     * Delete File
     * 
     * Deletes a file from a mod
     *
     * @param Mod $mod
     * @param File $img
     * @return void
     */
    public function deleteModFile(Request $request, Mod $mod, File $file)
    {
        $file->delete(); //Deletion of files handled in the model class.
    }

    /**
     * Upload File
     * 
     * Uploads a single file to a mod
     *
     * @param Request $request
     * @param Mod $mod
     * @return void
     */
    public function uploadModFile(Request $request, Mod $mod)
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
        $file->storePubliclyAs('files', $fileName);
        
        $file = File::create([
            'name' => $file->getClientOriginalName(), //This should be safe to just store in the DB, not the actual stored file name.
            'desc' => '', //TODO: Should be nullable
            'user_id' => $user->id,
            'mod_id' => $mod->id,
            'file' => $fileName,
            'type' => $fileType,
            'size' => $file->getSize()
        ]);

        $mod->save();

        return $file;
    }
}
