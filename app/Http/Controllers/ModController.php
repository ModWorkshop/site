<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Requests\ModUpsertRequest;
use App\Http\Resources\ModResource;
use App\Models\Image;
use App\Models\Mod;
use App\Models\File;
use App\Models\ModDownload;
use App\Models\ModLike;
use App\Models\ModMember;
use App\Models\ModView;
use App\Models\TransferRequest;
use App\Models\User;
use App\Models\Visibility;
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
        $val = $request->val([
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
            'sort_by' => Rule::in(['bumped_at', 'published_at', 'likes', 'downloads', 'views', 'score'])
        ]);
        
        /**
         * @var User
         */
        $user = $request->user();
        
        /**
         * @var Builder
         */
        $mods = Mod::queryGet($val, function(Builder $query, array $val) use ($user) {
            $sortBy = $val['sort_by'] ?? 'bumped_at';

            $query->orderByRaw("{$sortBy} DESC NULLS LAST");

            // If a guest or a user that doesn't have the edit-mod permission then we should hide any invisible or suspended mod
            if (!isset($user) || !$user->hasPermission('edit-mod')) {
                $query->where('visibility', Visibility::pub)->where('suspended', false);
                
                
                if (isset($user)) {
                    $query->orWhereRelation('members', 'user_id', $user->id);
                    $query->orWhere('submitter_id', $user->id);
                }
            }

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
        $mod->load('transferRequest');
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
                    $download = $mod->links->find($downloadId);
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
            if (!$request->boolean('silent')) {
                //We changed the version, update mod.
                if ($val['version'] !== $mod->version) {
                    $val['bumped_at'] = Carbon::now();
                }
            }
            $mod->calculateFileStatus(false);
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

        $mod->refresh();
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
            'file' => 'required|max:512000|mimes:png,jpg,webp,gif'
        ]);

        $user = $request->user();
        /**
         * @var UploadedFile $file
         */
        $file = $val['file'];
        $fileType = $file->extension();
        $fileName = $user->id.'_'.time().'_'.md5(uniqid(rand(), true)).'.'.$fileType;
        $file->storePubliclyAs('mods/images', $fileName, 'public');
        
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

    }

    /**
     * Registers a view for a mod, doesn't let you 'view' it twice
     * Works with guests
     *
     * @param Request $request
     * @param Mod $mod
     * @return void
     */
    public function registerView(Request $request, Mod $mod)
    {
        $user = $request->user();
        $ip = $request->ip();

        if (
            (isset($user) && ModView::where('user_id', $user->id)->where('mod_id', $mod->id)->exists()) 
        || (!isset($user) && ModView::where('ip_address', $ip)->where('mod_id', $mod->id)->exists())
        ) {
            return;
        }

        $view = new ModView();
        $view->mod_id = $mod->id;
        if (isset($user)) {
            $view->user_id = $user->id;
        }

        $view->ip_address = $ip;

        $view->save();
        $mod->views++;
        $mod->save();
        
        return response()->noContent(201);
    }

    /**
     * Registers a download for a mod, doesn't let you 'download' it twice
     * Works with guests
     *
     * @param Request $request
     * @param Mod $mod
     * @return void
     */
    public function registerDownload(Request $request, Mod $mod)
    {
        $user = $request->user();
        $ip = $request->ip();

        if (
            (isset($user) && ModDownload::where('user_id', $user->id)->where('mod_id', $mod->id)->exists()) 
        || (!isset($user) && ModDownload::where('ip_address', $ip)->where('mod_id', $mod->id)->exists())
        ) {
            return;
        }

        $download = new ModDownload();
        $download->mod_id = $mod->id;
        if (isset($user)) {
            $download->user_id = $user->id;
        }

        $download->ip_address = $ip;
        
        $download->save();
        $mod->downloads++;
        $mod->save();

        return response()->noContent(201);
    }

    /**
     * Toggles the state of the like of the mod
     *
     * @param Request $request
     * @param Mod $mod
     * @return void
     */
    public function toggleLike(Request $request, Mod $mod)
    {
        $user = $request->user();

        $liked = true;

        /**
         * @var ModLike
         */
        $like = ModLike::where('user_id', $user->id)->where('mod_id', $mod->id)->first();
        if (isset($like)) {
            $like->delete();
            $liked = false;
            $mod->likes--;
        } else {
            $like = new ModLike;
            $like->mod_id = $mod->id;
            $like->user_id = $user->id;
            $like->save();
            $mod->likes++;
        }

        $mod->save();

        return ['liked' => $liked, 'likes' => $mod->likes];
    }

    /**
     * Creates a transfer request, only once a user accepts can the mod be fully transfered. 
     *
     * @param Request $request
     * @param Mod $mod
     * @return void
     */
    public function transferOwnership(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'owner_id' => 'integer|required|min:1|exists:users,id',
            'keep_owner_level' => 'integer|nullable|min:0|max:3'
        ]);

        $user = User::find($val['owner_id']);

        if ($mod->transferRequest()->exists()) {
            abort(401, 'Transfer request in progress.');
        }

        $transferRequest = new TransferRequest(['keep_owner_level' => $val['keep_owner_level']]);
        $transferRequest->mod()->associate($mod);
        $transferRequest->user()->associate($user);

        $transferRequest->save();
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Mod $mod
     * @return void
     */
    public function acceptTransferRequest(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'accept' => 'boolean|required'
        ]);

        $user = $request->user();
        $transferRequest = $mod->transferRequest()->where('user_id', $user->id)->findOrFail();

        $transferRequest->delete();
        if ($val['accept']) {
            $mod->update(['submitter_id' => $user->id]);
        }
    }
}
