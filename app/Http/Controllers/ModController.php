<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetModsRequest;
use App\Http\Requests\ModUpsertRequest;
use App\Http\Resources\ModResource;
use App\Models\Category;
use App\Models\Game;
use App\Models\Image;
use App\Models\Mod;
use App\Models\ModDownload;
use App\Models\ModLike;
use App\Models\ModView;
use App\Models\Notification;
use App\Models\PopularityLog;
use App\Models\Setting;
use App\Models\Suspension;
use App\Models\Tag;
use App\Models\TransferRequest;
use App\Models\User;
use App\Services\APIService;
use App\Services\ModService;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Jcupitt\Vips;
use Illuminate\Validation\Rules\File;

/**
 * @group Mods
 */
class ModController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Mod::class, 'mod');
    }

    /**
     * Mods
     * 
     * Returns many mods, has a few options for searching the right mods
     *
     * @param ModUpsertRequest $request
     * return \Illuminate\Http\Response
     */
    public function index(GetModsRequest $request, Game $game=null)
    {
        $val = $request->val();

        if (isset($game)) {
            $mods = $game->mods()->without('game');
        } else {
            $mods = Mod::query();
        }
        
        $mods = $mods->queryGet($val, ModService::filters(...), true);
        return ModResource::collection($mods);
    }

    /**
     * Returns mods the user liked
     */
    public function liked(GetModsRequest $request)
    {
        $val = $request->val();
        $mods = Mod::queryGet($val, function($q, $val) {
            $q->whereHas('liked');
            ModService::filters($q, $val);
        }, true);
        return ModResource::collection($mods);

    }

    /**
     * Returns mods waiting for approval (approval == null)
     */
    public function waiting(GetModsRequest $request, Game $game=null)
    {
        $this->authorize('manageAny', [Mod::class, $game]);

        $val = $request->val();

        $mods = Mod::queryGet($val, function($q, $val) {
            $q->whereNull('approved');
            ModService::filters($q, $val);
        }, true);
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
    public function show(Game $game=null, Mod $mod)
    {
        $mod->withAllRest();
        APIService::setCurrentGame($mod->game);
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
    public function update(ModUpsertRequest $request, Game $game=null, Mod $mod=null)
    {
        $val = $request->validated();

        //Currently if we give this something like short_desc= we expect short_desc to get empty
        //However, Laravel sees this as a null value, while useful for integer filters, usually not so much for updating strings.
        //We should *not* remove the middleware that handles this as it can cause other issues.
        //This is the current solution:

        APIService::nullToEmptyStr($val,
            'short_desc',
            'donation',
            'license',
            'changelog',
            'instructions',
            'version',
        );

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
        
        $sendForApproval = Arr::pull($val, 'send_for_approval', false);
        if ($sendForApproval) {
            $val['approved'] = null;
        } else {
            $categoryId = $val['category_id'] ?? null;
            if (isset($categoryId) && (!isset($mod) || $mod->category_id !== $categoryId)) {
                $category = Category::find($categoryId);
                if ($category->approval_only) {
                    $val['approved'] = null;
                }
            }
        }

        if (isset($mod)) {
            if (!$request->boolean('silent')) {
                //We changed the version, update mod.
                if (isset($val['version']) && $val['version'] !== $mod->version) {
                    $followers = $mod->followers;
                    foreach ($followers as $follow) {
                        Notification::send(
                            notifiable: $mod,
                            user: $follow->user,
                            type: "follow_mod_new_version",
                            data: ['version' => $val['version']]
                        );
                    }

                    $mod->bump(false);
                }
            }

            //Only moderators are allowed to change games of mods
            if (!$this->user()->hasPermission('manage-mods')) {
                unset($val['game_id']);
            } else if (isset($val['game_id']) && (int)$val['game_id'] !== $mod->game_id) {
                Game::where('id', $val['game_id'])->increment('mods_count');
                $mod->game->decrement('mods_count');
            }

            $mod->calculateFileStatus(false);
            $mod->update($val);
        } else {
            $val['user_id'] = $request->user()->id;
            $val['game_id'] = $game->id;

            $mod = Mod::create($val);
        }

        Mod::flushQueryCache();
        Tag::flushQueryCache();

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
    public function store(ModUpsertRequest $request, Game $game)
    {
        return $this->update($request, $game);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mod $mod, Game $game=null)
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
        if ($mod->images()->count() >= Setting::getValue('mod_max_image_count')) {
            abort(406, 'Reached maximum allowed images for the mod!');
        }

        $val = $request->validate([
            'file' => ['required', File::image()->max(Setting::getValue('image_max_file_size') / 1024)]
        ]);

        /** @var UploadedFile $file */
        $file = $val['file'];

        ['name' => $name, 'type' => $type] = APIService::storeImage($file, 'mods/images', null, 300);

        $img = Image::create([
            'user_id' => $this->userId(),
            'mod_id' => $mod->id,
            'file' => $name,
            'has_thumb' => true,
            'type' => $type,
            'size' => 0, //TODO filesize($dir.'/'.$fileName)
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

        PopularityLog::log($mod, 'view');

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

        PopularityLog::log($mod, 'down');

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
            PopularityLog::deleteLog($mod, 'like');
        } else {
            $like = new ModLike;
            $like->mod_id = $mod->id;
            $like->user_id = $user->id;
            $like->save();
            $mod->increment('likes');

            PopularityLog::log($mod, 'like');
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

    public function approve(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'status' => 'boolean|required',
            'notify' => 'boolean',
            'reason' => 'string|max:1000'
        ]);

        $approve = $val['status'];
        if ($val['notify'] ?? true) {
            $members = [$mod->user, ...$mod->membersThatCanEdit];

            foreach ($members as $member) {
                Notification::send(
                    type: $approve ? 'mod_approved' : 'mod_rejected',
                    user: $member,
                    hideSender: true,
                    notifiable: $mod,
                    data: ['reason' => $val['reason']],
                );
            }
        }

        $mod->update(['approved' => $approve]);
    }

    public function suspend(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'status' => 'boolean|required',
            'notify' => 'boolean',
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

        $suspend = $val['status'];
        $notify = $val['notify'] ?? true;

        if ($val['status'] === true) {
            $suspension = Suspension::create([
                'reason' => $val['reason'],
                'mod_id' => $mod->id,
                'mod_user_id' => $request->user()->id //The moderator that suspended it
            ]);
        }

        if ($notify) {
            $members = [$mod->user, ...$mod->membersThatCanEdit];

            foreach ($members as $member) {
                Notification::send(
                    type: $suspend ? 'mod_suspended' : 'mod_unsuspended',
                    user: $member,
                    hideSender: true,
                    notifiable: $mod
                );
            }
        }

        $mod->update(['suspended' => $suspend]);

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

    /**
     * Reports the resource for moderators to look at.
     */
    public function report(Request $request, Mod $mod)
    {
        APIService::report($request, $mod);
    } 
}
