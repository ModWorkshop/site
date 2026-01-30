<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailVerifyRequest;
use App\Http\Requests\FilteredRequest;
use App\Http\Requests\GetThreadRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ModResource;
use App\Http\Resources\ThreadResource;
use App\Http\Resources\UserResource;
use App\Models\Game;
use App\Models\Mod;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use App\Services\APIService;
use App\Services\CommentService;
use App\Services\ThreadService;
use Auth;
use DB;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Str;

/**
 * @group Users
 *
 * API routes for interacting with users
 */
class UserController extends Controller
{
    public function __construct() {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Get List of Users
     */
    public function index(FilteredRequest $request, Game $game=null)
    {
        $val = $request->val([
            'id' => 'integer|min:1',
            'role_ids' => 'array|max:10',
            'role_ids.*' => 'integer|min:1|nullable',
            'game_role_ids' => 'array|max:10',
            'game_role_ids.*' => 'integer|min:1|nullable',
        ]);

        if (isset($game)) {
            APIService::setCurrentGame($game);
        }

        $query = Arr::pull($val, 'query');

        $users = User::queryGet($val, function($q, $val) use ($game, $query) {
            $q->withCount('viewableMods');

            if (isset($val['id'])) {
                $q->where('id', $val['id']);
            }
            if (isset($query) && !empty($query)) {
                if (ctype_digit($query) && $query < PHP_INT_MAX) {
                    $q->where('id', $query);
                }
                if (mb_strlen($query) > 2) {
                    $q->orWhere(fn($q) => $q->whereRaw('unique_name % ?', $query)->orWhereRaw("unique_name ILIKE '%' || ? || '%'", $query));
                    $q->orWhere(fn($q) => $q->whereRaw('name % ?', $query)->orWhereRaw("name ILIKE '%' || ? || '%'", $query));
                }
            }
            if (isset($val['role_ids'])) {
                $roleIds = array_filter($val['role_ids'], fn($id) => $id != 1);
                if (!empty($roleIds)) {
                    $q->whereHasIn('roles', fn($q) => $q->whereIn('roles.id', $val['role_ids']));
                }
            }
            if (isset($game) && isset($val['game_role_ids'])) {
                $roleIds = $val['game_role_ids'];
                if (!empty($roleIds)) {
                    $q->whereHasIn('gameRoles', fn($q) => $q->whereIn('game_roles.id', $val['game_role_ids']));
                }
            }

            if (isset($query) && mb_strlen($query) > 2) {
                if (ctype_digit($query) && $query < PHP_INT_MAX) {
                    $q->orderByRaw("
                        id = CAST($1 AS INTEGER) DESC,
                        unique_name = $1 DESC,
                        unique_name ILIKE '%' || $1 || '%' DESC,
                        unique_name % $1 DESC,
                        name ILIKE '%' || $1 || '%' DESC,
                        name % $1 DESC
                    ");
                } else {
                    $q->orderByRaw("
                        unique_name = $1 DESC,
                        unique_name ILIKE '%' || $1 || '%' DESC,
                        unique_name % $1 DESC,
                        name ILIKE '%' || $1 || '%' DESC,
                        name % $1 DESC
                    ");
                }
            }

            $q->orderBy('id');
        });

        return UserResource::collectionResponse($users);
    }

    /**
     * Get User
     *
     * @urlParam user integer required The ID of the user
     */
    public function getUser(string $user, Game $game=null)
    {
        if (isset($game)) {
            APIService::setCurrentGame($game);
        }

        $foundUser = null;

        if (ctype_digit($user) && $user < PHP_INT_MAX) {
            $foundUser = User::where('id', $user)->with('extra')->firstOrFail();
        } else {
            $foundUser = User::where(DB::raw('LOWER(unique_name)'), Str::lower($user))->with('extra')->firstOrFail();
        }

        $foundUser->loadCount('viewableMods');
        $foundUser->loadMissing('blockedUsers');
        if (Auth::hasUser()) {
            $foundUser->loadMissing('followed');
        }

        if ($foundUser->id === $this->userId() || Auth::user()?->hasPermission('manage-users')) {
            $foundUser->append('signable');
        }

        return new UserResource($foundUser);
    }

    public function updateCurrent(Request $request, Authenticatable $user) {
        return $this->update($request, $user);
    }

    /**
     * Edit User
     *
     * @authenticated
     */
    public function update(Request $request, User $user)
    {
        $passwordRule = APIService::getPasswordRule();
        $canManageUsers = Auth::user()->hasPermission('manage-users');

        $sorting = [
            'bumped_at',
            'published_at',
            'likes',
            'downloads',
            'views',
            'score',
            'weekly_score',
            'daily_score',
            'random',
            'name'
        ];

        $valRules =[
            'name' => 'string|nullable|min_strict:3|max:30',
            'unique_name' => ['alpha_dash:ascii', 'not_regex:/^\d+$/', 'nullable', 'min:3', 'max:50'],
            'avatar_file' => ['nullable', 'is_image'],
            'custom_color' => 'string|max:7|nullable',
            'bio' => 'string|spam_check|nullable|max:3000',
            'email' => 'email|nullable|max:255',
            'custom_title' => 'string|spam_check|nullable|max:100',
            'private_profile' => 'boolean',
            'invisible' => 'boolean',
            'banner_file' => ['nullable', 'is_image'],
            'background_file' => ['nullable', 'is_image'],
            'donation_url' => 'email_or_url|nullable|max:255',
            'show_tag' => 'in:role,supporter_or_role,none|nullable',
            'extra.default_mods_sort' => ['nullable', Rule::in($sorting)],
            'extra.home_default_mods_sort' => ['nullable', Rule::in($sorting)],
            'extra.game_default_mods_sort' => ['nullable', Rule::in($sorting)],
            'extra.default_mods_view' => ['nullable', Rule::in(['all', 'followed'])],
            'extra.home_show_last_games' => 'boolean|nullable',
            'extra.home_show_mods' => 'boolean|nullable',
            'extra.home_show_threads' => 'boolean|nullable',
            'extra.game_show_mods' => 'boolean|nullable',
            'extra.game_show_threads' => 'boolean|nullable',
            'extra.auto_subscribe_to_mod' => 'boolean|nullable',
            'extra.auto_subscribe_to_thread' => 'boolean|nullable',
            'extra.background_opacity' => 'numeric|min:0|max:1|nullable',
            'extra.developer_mode' => 'boolean|nullable',
        ];

        if ($user->signable && !$canManageUsers) {
            $valRules['password'] = ['nullable', 'required_with:current_password', $passwordRule, 'max:128'];
            $valRules['current_password'] = ['nullable', 'required_with:password'];
        } else {
            $valRules['password'] = ['nullable', $passwordRule, 'max:128'];
        }

        $val = $request->validate($valRules);

        APIService::normalizeStrings($val, 'name');

        APIService::nullToUndefined($val, 'name', 'unique_name', 'email');

        APIService::nullToEmptyStr($val,
            'custom_color',
            'bio',
            'custom_title',
            'donation_url',
            'avatar_file',
            'banner_file',
            'background_file'
        );

        $extra = Arr::pull($val, 'extra');

        if (isset($val['unique_name'])) {
            $val['unique_name'] = Str::lower($val['unique_name']);

            if ($val['unique_name'] != Str::lower($user->unique_name)) {
                if (User::where(DB::raw('LOWER(unique_name)'), $val['unique_name'])->exists()) {
                    abort(422, 'Unique name or email already used!');
                }
            }
        }

        if (isset($val['email']) && $val['email'] != $user->email) {
            if (User::where(DB::raw('LOWER(email)'), Str::lower($val['email']))->exists()) {
                abort(422, 'Unique name or email already used!');
            }
        }

        $trustLevel = $user->getTrustLevel();
        $banned = $user->isBanned();
        if ($trustLevel == 0 || $banned) {
            $values = ['bio', 'avatar_file', 'banner_file', 'background_file', 'custom_title', 'donation_url'];
            foreach ($values as $value) {
                if (!empty($val[$value])) {
                    if ($banned) {
                        abort(422, 'Banned users cannot set these fields!');
                    } else {
                        abort(422, 'You must be verified to set these fields!');
                    }
                }
            }
        }
        // Verify donation links
        if (!empty($val['donation_url']) && APIService::checkDonationLink($val['donation_url']) == null) {
            abort(422, 'Invalid donation link!');
        }

        $avatarFile = Arr::pull($val, 'avatar_file');
        APIService::storeImage($avatarFile, 'users/images', $user->avatar, [
            'size' => 256,
            'thumbnailSize' => 64,
            'allowDeletion' => true,
            'onSuccess' => function($path) use ($user) {
                $user->avatar = $path;
                $user->avatar_has_thumb = strlen($path) > 0;
            },
        ]);

        $bannerFile = Arr::pull($val, 'banner_file');
        APIService::storeImage($bannerFile, 'users/images', $user->banner, [
            'allowDeletion' => true,
            'onSuccess' => fn($path) => $user->banner = $path
        ]);

        $backgroundFile = Arr::pull($val, 'background_file');
        APIService::storeImage($backgroundFile, 'users/images', $user->background, [
            'allowDeletion' => true,
            'onSuccess' => fn($path) => $user->extra->background = $path
        ]);

        //Change password code
        $password = Arr::pull($val, 'password');
        if (isset($password)) {
            //We must require current password, but remember that there are accounts that sign in using SSO.
            if (!isset($user->password) || $canManageUsers || Hash::check(Arr::pull($val, 'current_password'), $user->password)) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
            } else {
                abort(422, 'Current password is incorrect');
            }
        }

        $email = Arr::pull($val, 'email');
        if (isset($email) && Str::lower($user->email) != Str::lower($email)) {
            if (!isset($user->password)) {
                abort(422, 'Password is required to be set to set email!');
            }
            $user->setEmail(Str::lower($email));
        }

        $user->update($val);
        if (isset($extra)) {
            $user->extra->update($extra);
        }

        $user->refresh();
        $user->load('extra');
        $user->load('roles.permissions');
        $user->append('signable');

        return new UserResource($user);
    }

    /**
     * Set User Roles
     *
     * @authenticated
     */
    public function setUserRoles(Request $request, User $user) {
        $this->authorize('manageRoles', $user);

        $val = $request->validate([
            'role_ids' => 'array',
            'role_ids.*' => 'integer|min:2|exists:roles,id',
        ]);

        [$attach, $detach] = $user->syncRoles(array_map('intval', array_unique(array_filter($val['role_ids'], fn($val) => is_numeric($val)))));

        AuditLog::logUpdate($user, [
            '$added' => ['role' => $attach],
            '$removed' => ['role' => $detach],
        ]);
    }

    /**
     * Delete a user
     *
     * @authenticated
     */
    public function destroy(Request $request, User $user)
    {
        $val = $request->validate([
            'unique_name' => ['alpha_dash:ascii', 'not_regex:/^\d+$/', 'nullable', 'min:3', 'max:50'],
            'are_you_sure' => 'required|boolean',
        ]);

        APIService::checkCaptcha($request);

        if (!$val['are_you_sure'] || $val['unique_name'] !== $user->unique_name) {
            abort(406, 'The unique name must match and you must agree to the deletion!');
        }

        $user->delete();
    }

    /**
     * Get Current User
     *
     * Returns the currently authenticated user
     *
     * @authenticated
     */
    public function currentUser(Request $request)
    {
        $user = $request->user();
        $user->append('signable');
        $user->load('extra');

        return new UserResource($user);
    }

    /**
     * Get User Data
     *
     * Returns GDPR compliant user data we store.
     */
    public function userData()
    {
        ini_set('memory_limit','1024M');
        ini_set('max_execution_time','1800');

        $user = $this->user();
        $userData = [
            'user' => new UserResource($user),
            'mods' => ModResource::collection($user->mods()->without(Mod::DEFAULT_MOD_WITH)->with('files')->get()),
            'threads' => ThreadResource::collection($user->threads()->setEagerLoads([])->get()),
            'comments' => CommentResource::collection($user->comments()->setEagerLoads([])->get()),
            'blocked_users' => $user->allBlockedUsers,
            'blocked_tags' => $user->allBlockedTags,
            'followed_mods' => $user->allFollowedMods,
            'followed_users' => $user->allFollowedUsers,
            'followed_games' => $user->allFollowedGames,
        ];
        return response()->streamDownload(function () use ($userData) {
            echo json_encode($userData, JSON_PRETTY_PRINT);
        }, 'user-data.json');
    }

    /**
     * Report a user
     *
     * @group Reports
     * @authenticated
     */
    public function report(Request $request, User $user)
    {
        APIService::report($request, $user);
    }

    /**
     * @hideFromApiDocumentation
     */
    public function purgeUser(User $user) {
        $me = $this->user();

        if ($me->hasPermission('manage-mods')) {
            foreach ($user->mods as $mod) {
                $mod->delete();
            }
        }

        if ($me->hasPermission('manage-discussions')) {
            foreach ($user->threads as $thread) {
                $thread->delete();
            }

            foreach ($user->comments as $comment) {
                $comment->delete();
            }
        }

        APIService::deleteImage('users/images', $user->avatar);
        APIService::deleteImage('users/images', $user->banner);
        $user->update([
            'avatar' => '',
            'banner' => '',
            'bio' => '',
            'custom_title' => '',
        ]);
    }

    /**
     * Delete all user mods
     *
     * @authenticated
     * @hideFromApiDocumentation
     */
    public function deleteMods(User $user)
    {
        foreach ($user->mods as $mod) {
            $mod->delete();
        }
    }

    /**
     * Delete all user discussions
     *
     * @authenticated
     * @hideFromApiDocumentation
     */
    public function deleteDiscussions(User $user)
    {
        foreach ($user->threads as $thread) {
            $thread->delete();
        }

        foreach ($user->comments as $comment) {
            $comment->delete();
        }
    }

    /**
     * Get user comments
     *
     * @group Comments
     * @authenticated
     */
    public function getComments(FilteredRequest $request, User $user) {
        return CommentService::index($request, $user, [
            'commentable_is_user' => true,
            'include_replies' => true,
            'include_last_replies' => false,
            'orderBy' => 'created_at DESC'
        ]);
    }

    /**
     * Get user threads
     *
     * @group Threads
     * @authenticated
     */
    public function getThreads(GetThreadRequest $request, User $user) {
        return ThreadResource::collectionResponse(ThreadService::threads($request->val(), null, $user->threads()->getQuery()));
    }

    /**
     * Verifies email via a link sent to the email
     *
     * @hideFromApiDocumentation
     */
    public function verifyEmail(EmailVerifyRequest $request)
    {
        $request->fulfill();
        $user = $this->user();
        $user->activated = true;
        $user->save();
    }

    /**
     * Resends email verification to user's email
     *
     * @hideFromApiDocumentation
     */
    public function resendEmail(Request $request)
    {
        $request->user()->sendEmailVerification();
    }

    /**
     * Cancels pending email if the user changes their mind.
     *
     * @hideFromApiDocumentation
     */
    public function cancelPendingEmail()
    {
        $this->user()->forceFill(['pending_email' => null, 'pending_email_set_at' => null])->save();
    }
}
