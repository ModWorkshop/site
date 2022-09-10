<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetModsRequest;
use App\Http\Requests\ModUpsertRequest;
use App\Http\Resources\ModResource;
use App\Models\Image;
use App\Models\Mod;
use App\Models\ModDownload;
use App\Models\ModLike;
use App\Models\ModView;
use App\Models\Notification;
use App\Models\Suspension;
use App\Models\TransferRequest;
use App\Models\User;
use App\Services\APIService;
use App\Services\ModService;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Jcupitt\Vips;

const animated = [
    'gif' => true,
    'webp' => true,
    'avif' => true,
    'jxl' => true
    // 'png' => true, https://github.com/libvips/libvips/issues/2537
    // 'apng' => true
];

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
    public function index(GetModsRequest $request)
    {
        $val = $request->val();
        $mods = Mod::queryGet($val, ModService::filters(...), true);
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
        $mod->withAllRest();
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

        APIService::nullToEmptyStr($val, 'short_desc');
        APIService::nullToEmptyStr($val, 'donation');
        APIService::nullToEmptyStr($val, 'license');
        APIService::nullToEmptyStr($val, 'changelog');
        APIService::nullToEmptyStr($val, 'version');

        $val['legacy_banner_url'] = ''; //User is warned about this in the edit mod pagew

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
                if (isset($val['version']) && $val['version'] !== $mod->version) {
                    $mod->bump(false);
                }
            }
            $mod->calculateFileStatus(false);
            $mod->update($val);
        } else {
            // Never put something like $request->all(); in create.
            //Laravel may have guard for this, but there's really no reason what to so ever to give it that.
            $val['user_id'] = $request->user()->id;
            $mod = Mod::create($val); // Validate handles the important stuff already.
        }

        if(isset($tags)) {
            $mod->tags()->sync($tags);
        }

        $mod->refresh();
        $mod->withAllRest();

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
    public function destroy(Mod $mod)
    {
        $mod->delete();
    }

    /**
     * Upload mod image
     * 
     * @param Request $request
     * @param Mod $mod
     * @return void
     */
    public function uploadModImage(Request $request, Mod $mod) {
        if ($mod->images()->count() >= 20) {
            abort(406, 'Reached maximum allowed images for the mod!');
        }

        $val = $request->validate([
            'file' => 'required|max:512000|mimes:png,jpg,webp,gif'
        ]);

        $dir = Storage::disk('public')->path('mods/images');

        $user = $request->user();
        /**
         * @var UploadedFile $file
         */
        $file = $val['file'];
        $fileName = $user->id.'_'.time().'_'.md5(uniqid(rand(), true)).'.webp';
        $fileType = $file->extension();
        $opts = isset(animated[$fileType]) ? '[n=-1]' : '';

        $img = Vips\Image::newFromFile($file->path().$opts);
        $img->writeToFile($dir.'/'.$fileName, ["Q" => 80]);

        $thumb = $img->thumbnail_image(300);
        $thumb->writeToFile($dir.'/thumb_'.$fileName);

        $img = Image::create([
            'user_id' => $user->id,
            'mod_id' => $mod->id,
            'file' => $fileName,
            'has_thumb' => true,
            'type' => $file->extension(),
            'size' => filesize($dir.'/'.$fileName)
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
            $mod->decrement('likes');
        } else {
            $like = new ModLike;
            $like->mod_id = $mod->id;
            $like->user_id = $user->id;
            $like->save();
            $mod->increment('likes');
        }

        $mod->likes = max(0, $mod->likes);
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
            abort(405, 'Transfer request in progress.');
        }

        $transferRequest = new TransferRequest(['keep_owner_level' => $val['keep_owner_level']]);
        $transferRequest->mod()->associate($mod);
        $transferRequest->user()->associate($user);

        Notification::send(
            notifiable: $mod,
            user: $user,
            type: 'transfer_ownership'
        );

        $transferRequest->save();

        return $transferRequest;
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
        $userId = $user->id;
        $transferRequest = $mod->transferRequest()->where('user_id', $userId)->firstOrFail();

        //Keep owner as a member
        if (isset($transferRequest->keep_owner_level)) {
            $mod->members()->attach($mod->user->id, ['level' => $transferRequest->keep_owner_level, 'accepted' => true]);
        }

        Notification::deleteRelated($mod, 'transfer_ownership');

        $transferRequest->delete();
        if ($val['accept']) {
            $mod->update(['user_id' => $userId]);
        }

        //Remove the new owner from the members
        $mod->members()->detach($user);
        Notification::deleteRelated($user, 'membership_request'); //Just to be sure
    }

    public function cancelTransferRequest(Request $request, Mod $mod)
    {
        $transferRequest = $mod->transferRequest;
        
        if (!isset($transferRequest)) {
            abort(404, 'No transfer request exists');
        }

        $mod->transferRequest()->delete();
        Notification::deleteRelated($mod, 'transfer_ownership');
    }

    public function suspend(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'status' => 'boolean|required',
            'reason' => 'string|min:3|max:1000'
        ]);

        $lastSuspension = $mod->lastSuspension;

        if ($val['status'] === $mod->suspended) {
            abort(409);
        }

        if (isset($lastSuspension)) {
            $lastSuspension->update(['status' => false]); //Last suspension, if exists, is no longer valid.
        }

        $suspension = null;
        if ($val['status'] === true) {
            $suspension = Suspension::create([
                'reason' => $val['reason'],
                'mod_id' => $mod->id,
                'mod_user_id' => $request->user()->id //The moderator that suspended it
            ]);
        }

        $mod->update(['suspended' => $val['status']]);

        return $suspension;
    }

    /**
     * Deletes all images of a mod
     *
     * @param Mod $mod
     * @return void
     */
    public function deleteAllImages(Mod $mod)
    {
        foreach ($mod->images as $image) {
            $image->delete();
        }
    }
}
