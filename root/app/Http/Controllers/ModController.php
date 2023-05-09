<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetModsRequest;
use App\Http\Requests\ModUpsertRequest;
use App\Http\Resources\ModResource;
use App\Models\Category;
use App\Models\Game;
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
use App\Services\Utils;
use Arr;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Str;

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
     * 
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
     * Liked Mods
     * 
     * Returns mods the user liked
     * 
     * @authenticated
     */
    public function liked(GetModsRequest $request)
    {
        $val = $request->val();
        $mods = Mod::queryGet($val, function($q, $val) {
            $q->whereHasIn('liked');
            ModService::filters($q, $val);
        }, true);

        return ModResource::collection($mods);
    }

    /**
     * Waiting for approval
     * 
     * Returns mods waiting for approval (approval == null)
     * 
     * @authenticated
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
     * Get Mod
     * 
     * Returns a single mod
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
        
        $publish = Arr::pull($val, 'publish', false);
        $sendForApproval = Arr::pull($val, 'send_for_approval', false);

        $categoryId = $val['category_id'] ?? null;
        $gameId = $game?->id ?? $val['game_id'] ?? $mod?->game_id;
        $category = null;

        if (isset($val['category_id'])) {
            $category = Category::find($categoryId);
        }

        if ($sendForApproval) {
            $val['approved'] = null;
        } else {
            if (isset($categoryId) && (!isset($mod) || $mod->category_id !== $categoryId)) {
                if ($category->approval_only) {
                    $val['approved'] = null;
                }
            }
        }

        $sendDiscordApproval = !isset($mod) || (array_key_exists('approved', $val) && $val['approved'] !== $mod->approved);

        if (isset($category) && $category->game_id !== $gameId) {
            abort(409, 'Invalid category. It must belong to the game.');
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

            //Only moderators are allowed to change games of mods and allowed storage
            if (!$this->user()->hasPermission('manage-mods')) {
                unset($val['game_id']);
                unset($val['allowed_storage']);
            } else if (isset($val['game_id']) && (int)$val['game_id'] !== $mod->game_id) {
                Game::where('id', $val['game_id'])->increment('mod_count');
                $mod->game->decrement('mod_count');
            }

            $mod->calculateFileStatus(false);
            $mod->update($val);
        } else {
            if (!$this->user()->hasPermission('manage-mods')) {
                unset($val['allowed_storage']);
            }

            $val['user_id'] = $request->user()->id;
            $val['game_id'] = $game->id;

            $mod = Mod::create($val);
        }

        Mod::flushQueryCache();
        Tag::flushQueryCache();

        if(isset($tags)) {
            $mod->tags()->sync($tags);
        }

        $currentTags = $mod->tags;
        foreach ($currentTags as $tag) {
            if (isset($tag->game_id) && $tag->game_id !== $mod->game_id) {
                $mod->tags()->detach($tag->id);
            }
        }

        $mod->refresh();
        $mod->withAllRest();

        if ($publish) {
            $mod->publish();
        }

        $send = [Setting::getValue('discord_approval_webhook')];

        if ($sendDiscordApproval && count($send)) {
            $siteUrl = env('FRONTEND_URL');
            Utils::sendDiscordMessage($send, "The mod **%s** is waiting for approval. {$siteUrl}/mod/%s", [$mod->name, $mod->id]);
        }

        return new ModResource($mod);
    }

    /**
     * Create Mod
     * 
     * Creates a new mod
     * 
     * @authenticated
     */
    public function store(ModUpsertRequest $request, Game $game)
    {
        return $this->update($request, $game);
    }

    /**
     * Delete Mod
     * 
     * @authenticated
     */
    public function destroy(Mod $mod, Game $game=null)
    {
        $mod->delete();
    }

    /**
     * Register a View
     * 
     * Registers a view for a mod, doesn't let you 'view' it twice
     * Works with guests
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
     * Register a Download
     * 
     * Registers a download for a mod, doesn't let you 'download' it twice
     * Works with guests
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
     * Toggle Like
     * 
     * Toggles the state of the like of the mod
     * 
     * @authenticated
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
     * Transfer Ownership
     * 
     * Creates a transfer request, only once a user accepts can the mod be fully transfered. 
     * 
     * @authenticated
     */
    public function transferOwnership(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'owner_id' => 'integer|required|min:1|exists:users,id',
            // Which level to keep the owner as. If null, won't keep the owner as a member.
            'keep_owner_level' => 'nullable|in:collaborator,maintainer,viewer,contributor'
        ]);

        $user = User::find($val['owner_id']);

        if (isset($user->last_ban) || isset($user->last_game_ban)) {
            abort(405, 'Cannot transfer ownership to banned users.');
        }

        if ($mod->user_id == $val['owner_id']) {
            abort(405, 'Cannot transfer ownership to the owner themselves.');
        }

        if ($mod->transferRequest()->exists()) {
            abort(409, 'Transfer request in progress.');
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
     * Accept Transfer Ownership Request
     * 
     * @authenticated
     */
    public function acceptTransferRequest(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'accept' => 'boolean|required'
        ]);

        $user = $this->user();
        $userId = $user->id;
        $transferRequest = $mod->transferRequest()->where('user_id', $userId)->firstOrFail();

        if ($val['accept']) {
            //Keep owner as a member
            if (isset($transferRequest->keep_owner_level)) {
                $mod->members()->attach($mod->user->id, ['level' => $transferRequest->keep_owner_level, 'accepted' => true]);
            }

            $mod->user()->decrement('mod_count');
            $user->increment('mod_count');
            $mod->update(['user_id' => $userId]);
        }

        $transferRequest->delete();

        //Remove the new owner from the members
        $mod->members()->detach($user);
    }

    /**
     * Cancel Transfer Ownership Request
     * 
     * @authenticated
     */
    public function cancelTransferRequest(Request $request, Mod $mod)
    {
        $transferRequest = $mod->transferRequest;
        
        if (!isset($transferRequest)) {
            abort(404, 'No transfer request exists');
        }

        $mod->transferRequest()->delete();
    }

    /**
     * Approve Mod
     * 
     * Approves a waiting for approval mod.
     * 
     * @authenticated
     */
    public function approve(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'status' => 'boolean|required',
            'notify' => 'boolean',
            'reason' => 'string|nullable|max:1000'
        ]);

        $approve = $val['status'];

        if ($mod->approved === $approve) {
            abort(405, 'Mod is already '.$approve ? 'approved' : 'rejected');
        }

        if ($val['notify'] ?? true) {
            $members = [$mod->user, ...$mod->membersThatCanEdit];

            foreach ($members as $member) {
                Notification::send(
                    type: $approve ? 'mod_approved' : 'mod_rejected',
                    user: $member,
                    hideSender: true,
                    notifiable: $mod,
                    data: isset($val['reason']) ? ['reason' => $val['reason']] : null,
                );
            }
        }

        $mod->update(['approved' => $approve]);
        if ($approve) {
            $mod->publish();
        }

        // Send to discord about this
        $send = [Setting::getValue('discord_approval_webhook')];
        if (count($send)) {
            $siteUrl = env('FRONTEND_URL');
            $status = $approve ? 'approved' : 'rejected';
            $reason = !$approve ? "\nReason: ".$val['reason'] : '';
            Utils::sendDiscordMessage($send, "The mod **%s** has been {$status}! <{$siteUrl}/mod/%s>.{$reason}", [
                $mod->name,
                $mod->id
            ]);
        }
    }

    /**
     * Suspend Mod
     * 
     * @authenticated
     */
    public function suspend(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'status' => 'boolean|required',
            'notify' => 'boolean',
            'reason' => 'string|min:3|max:1000'
        ]);

        $lastSuspension = $mod->lastSuspension;

        $suspend = $val['status'];

        if ($val['status'] === $mod->suspended) {
            abort(405, 'Mod is already '.($suspend ? 'suspended' : 'unsuspended'));
        }

        if (isset($lastSuspension)) {
            $lastSuspension->update(['status' => false]); //Last suspension, if exists, is no longer valid.
        }

        $suspension = null;

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
        // Send to discord about this
        $send = [Setting::getValue('discord_suspension_webhook')];
        if (count($send)) {
            $siteUrl = env('FRONTEND_URL');
            $moderator = Auth::user()->name;
            $userLink = env('FRONTEND_URL').'/user/'.($mod->unique_name ?? $mod->id);
            $status = $suspend ? 'suspended' : 'unsuspended';
            $case = Str::random(20);
            $message = Str::repeat('-', 100);
            $message .= "\nThe mod **%s**, which is owned by <$userLink>, has been {$status} by {$moderator}.";
            $message .= "\nLink to the mod: {$siteUrl}/mod/%s.";
            if (isset($val['reason'])) {
                $message .= "\nReason: {$val['reason']}\n";
            }
            $message .= Str::repeat('-', 30)."CASE {$case}".Str::repeat('-', 30);
            
            Utils::sendDiscordMessage($send, $message, [$mod->name, $mod->id]);
        }

        return $suspension;
    }

    /**
     * Report Mod
     * 
     * Reports the mod for moderators to look at it.
     * 
     * @authenticated
     */
    public function report(Request $request, Mod $mod)
    {
        APIService::report($request, $mod);
    } 
}
