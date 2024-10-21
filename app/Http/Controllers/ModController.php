<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetModsRequest;
use App\Http\Requests\ModUpsertRequest;
use App\Http\Resources\ModResource;
use App\Models\Category;
use App\Models\File;
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
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Str;

/**
 * @group Mods
 */
class ModController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Mod::class);
    }

    /**
     * Get List of Mods
     *
     * Returns many mods, has a few options for searching the right mods
     */
    public function index(GetModsRequest $request, Game $game=null)
    {
        $val = $request->val();

        if (isset($game)) {
            $mods = ModService::mods(val: $val, query: $game->mods(), cacheForGuests: $game->short_name.'-index');
        } else {
            $mods = ModService::mods($val, cacheForGuests: 'index');
        }

        return ModResource::collectionResponse($mods);
    }

    /**
     * Get List of Popular Mods
     */
    public function popularAndLatest(Request $request, Game $game=null)
    {
        if (isset($game)) {
            return [
                'latest' => ModService::mods(['limit' => 10], query: $game->mods(), cacheForGuests: 'index')->items(),
                'popular' => ModService::mods(['sort_by' => 'daily_score', 'limit' => 5], query: $game->mods(), cacheForGuests: 'index')->items(),
            ];
        } else {
            return [
                'latest' => ModService::mods(['limit' => 10], cacheForGuests: 'index')->items(),
                'popular' => ModService::mods(['sort_by' => 'daily_score', 'limit' => 5], cacheForGuests: 'index')->items(),
            ];
        }
    }

    /**
     * Get List of Liked Mods
     *
     * Returns mods the user liked
     *
     * @authenticated
     */
    public function liked(GetModsRequest $request)
    {
        $mods = ModService::mods($request->val(), function($q, $val) {
            $q->whereHasIn('liked');
        });

        return ModResource::collectionResponse($mods);
    }

    /**
     * List of Waiting for Approval Mods
     *
     * Returns mods waiting for approval (approval == null)
     *
     * @authenticated
     */
    public function waiting(GetModsRequest $request, Game $game=null)
    {
        $this->authorize('manageAny', [Mod::class, $game]);

        $mods = ModService::mods($request->val(), function($q, $val) {
            $q->whereNull('approved');
        }, $game?->mods());

        return ModResource::collectionResponse($mods);
    }

    /**
     * Get Mod
     *
     * Returns a single mod
     */
    public function show(Game $game=null, Mod $mod)
    {
        $mod->append('mod_managers');
        return new ModResource($mod);
    }

    /**
     * Edit Mod
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
                    $mod->downloadRelation()->associate($download);
                } else {
                    throw ValidationException::withMessages(['download_id' => "The download doesn't exist in the mod"]);
                }
            } else {
                $mod->downloadRelation()->dissociate();
            }
        }

        $tags = Arr::pull($val, 'tag_ids'); // Since 'tags' isn't really inside the model, we need to pull it out.

        $publish = Arr::pull($val, 'publish', false);
        $sendForApproval = Arr::pull($val, 'send_for_approval', false);

        $categoryId = $val['category_id'] ?? null;
        $gameId = $game?->id ?? $val['game_id'] ?? $mod?->game_id;
        $category = null;

        if (isset($val['category_id'])) {
            $category = Category::where('game_id', $gameId)->find($categoryId);
            if (!isset($category)) {
                abort(409, 'Invalid category. It must belong to the game.');
            }
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

        $sendDiscordApproval = false;
        if (array_key_exists('approved', $val) && $val['approved'] === null) {
            $sendDiscordApproval = !isset($mod) || $val['approved'] !== $mod->approved;
        }

        
        $val['parser_version'] = 2;

        if (isset($mod)) {
            // If any text gets changed, force the mod to parser V2.
            if ((isset($val['desc']) && $val['desc'] != $mod->desc)
                || (isset($val['license']) && $val['license'] != $mod->license)
                || (isset($val['changelog']) && $val['changelog'] != $mod->changelog)
                || (isset($val['instructions']) && $val['instructions'] != $mod->instructions)
            ) {
                $mod->parser_version = 2;
            }

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
            }

            $mod->calculateFileStatus(false);
            $mod->update($val);
        } else {
            if (!$this->user()->hasPermission('manage-mods')) {
                unset($val['allowed_storage']);
            }

            $val['user_id'] = $request->user()->id;
            $val['game_id'] = $gameId;
            $val['parser_version'] = 2;

            $mod = Mod::create($val);
        }

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
        $mod->withFetchResourceGame();

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
     * Deletes a mod and all of its contents.
     *
     * @authenticated
     */
    public function destroy(Mod $mod, Game $game=null)
    {
        $mod->delete();
    }

    /**
     * POST Register a View
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
        || ModView::where('ip_address', $ip)->where('mod_id', $mod->id)->exists()
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
        $mod->increment('views');

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
     * Transfer Ownership of a Mod
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

        if ($user->isBanned($mod->game_id)) {
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
        $moderator = $this->user();
        $send = [Setting::getValue('discord_approval_webhook')];
        if (count($send)) {
            $siteUrl = env('FRONTEND_URL');
            $status = $approve ? 'approved' : 'rejected';
            $reason = !$approve ? "\nReason: ".$val['reason'] : '';
            Utils::sendDiscordMessage($send, "The mod **%s** has been {$status} by {$moderator->name} <{$siteUrl}/mod/%s>.{$reason}", [
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
            $userLink = env('FRONTEND_URL').'/user/'.($mod->user_id);
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

    /**
     * Download First File
     *
     * Downloads the first available file
     *
     * @subgroup Files
     */
    function downloadPrimaryFile(Mod $mod) {
        $file = $mod->downloadRelation;
        if (!$file instanceof File) {
            $file = $mod->files()->firstOrFail();
        }
        if (isset($file)) {
            return redirect($file->downloadUrl);
        } else {
            return abort(404, 'Mod has no files!');
        }
    }

    /**
     * Get Mod Versions
     *
     * Returns a list of versions (Up to 100 mods)
     * Convenient way of getting many versions at once and avoid sending too many requests
     */
    public function getVersions(Request $request) {
        $val = $request->validate([
            'mod_ids' => 'array|required',
            'mod_ids.*' => 'integer|min:1',
        ]);

        $mods = ModService::mods(val: $val, query: Mod::whereIn('id', $val['mod_ids']));
        $onlyVersions = [];
        foreach($mods as $mod) {
            $onlyVersions[$mod->id] = $mod->version;
        }

        return $onlyVersions;
    }
}
