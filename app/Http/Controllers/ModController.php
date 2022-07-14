<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Requests\ModUpsertRequest;
use App\Http\Resources\ModResource;
use App\Models\Image;
use App\Models\Mod;
use App\Models\File;
use Arr;
use Carbon\Carbon;
use DB;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Log;
use Str;

/**
 * @group Mods
 */
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
     * @param ModUpsertRequest $request
     * return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request)
    {
        // Query parameters
        $val = $request->validate([
            // How many mods should this return. 
            'game_id' => 'integer|nullable|min:1|exists:games,id',
            'tags' => 'array',
            'tags.*' => 'integer|min:1|nullable',
            'categories' => 'array',
            'categories.*' => 'integer|min:1|nullable',
            'block_tags' => 'array',
            // Filter out mods that are in these tags
            'block_tags.*' => 'integer|min:1|exists:tags,id',
            'submitter_id' => 'integer|nullable|min:1',
            'sort_by' => Rule::in(['bump_date', 'publish_date', 'likes', 'downloads', 'views', 'score'])
        ]);
        
        /**
         * @var Builder
         */
        $mods = Mod::queryGet($val, function(Builder $query, array $val) {
            $sortBy = $val['sort_by'] ?? 'bump_date';

            $query->orderByRaw("{$sortBy} IS NOT NULL DESC");
            
            if (isset($val['game_id'])) {
                $query->where('game_id', $val['game_id']);
            }

            if (isset($val['submitter_id'])) {
                $query->where('submitter_id', $val['submitter_id']);
            }

            if (isset($val['tags'])) {
                $query->whereHasIn('tags', function(Builder $q) use ($val) {
                    $q->limit(1)->whereIn('tags.id', array_map('intval', $val['tags']));
                });
            }

            if (!empty($val['categories'])) {
                $query->whereIn('category_id', $val['categories']);
            }

            if (!empty($val['block_tags'])) { //Broken for some reason
                $query->whereHasIn('tags', function(Builder $q) use ($val) {
                    $q->whereIn('tags.id', array_map('intval', $val['block_tags']));
                });
            }

            if (isset($val['query']) && !empty($val['query'])) {
                $query->whereRaw("name % ?", [$val['query']]);
            }
        });

        return ModResource::collection($mods);
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
    public function update(ModUpsertRequest $request, Mod $mod=null)
    {
        $val = $request->validated();

        //Currently if we give this something like short_desc= we expect short_desc to get empty
        //However, Laravel sees this as a null value, while useful for integer filters, usually not so much for updating strings.
        //We should *not* remove the middleware that handles this as it can cause other issues.
        //This is the current solution:
        $val['short_desc'] ??= '';
        $val['donation'] ??= '';
        $val['license'] ??= '';
        $val['changelog'] ??= '';
        $val['version'] ??= '';

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

        $val['bump_date'] = Carbon::now();

        var_dump($val);

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
     * Create Mod
     * 
     * Creates a new mod
     * 
     * @authenticated
     *
     * @param ModUpsertRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ModUpsertRequest $request)
    {
        return $this->update($request);
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
