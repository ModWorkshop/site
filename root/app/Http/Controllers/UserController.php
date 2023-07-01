<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailVerifyRequest;
use App\Http\Requests\FilteredRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ModResource;
use App\Http\Resources\ThreadResource;
use App\Http\Resources\UserResource;
use App\Models\Game;
use App\Models\Mod;
use App\Models\Setting;
use App\Models\User;
use App\Services\APIService;
use Auth;
use DB;
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(FilteredRequest $request, Game $game=null)
    {
        $val = $request->val([
            'id' => 'integer|min:1',
            'role_ids.*' => 'array|max:10',
            'role_ids.*' => 'integer|min:1|nullable',
            'game_role_ids.*' => 'array|max:10',
            'game_role_ids.*' => 'integer|min:1|nullable',
        ]);

        if (isset($game)) {
            APIService::setCurrentGame($game);
        }

        $users = User::queryGet($val, function($query, $val) use ($game) {
            if (isset($val['id'])) {
                $query->where('id', $val['id']);
            }
            if (isset($val['query']) && !empty($val['query'])) {
                $query->orWhere(fn($q) => $q->whereRaw('unique_name % ?', $val['query'])->orWhere('unique_name', 'ILIKE', '%'.$val['query'].'%'));
            }
            if (isset($val['role_ids'])) {
                $roleIds = array_filter($val['role_ids'], fn($id) => $id != 1);
                if (!empty($roleIds)) {
                    $query->whereHasIn('roles', fn($q) => $q->whereIn('roles.id', $val['role_ids']));
                }
            }
            if (isset($game) && isset($val['game_role_ids'])) {
                $roleIds = $val['game_role_ids'];
                if (!empty($roleIds)) {
                    $query->whereHasIn('gameRoles', fn($q) => $q->whereIn('game_roles.id', $val['game_role_ids']));
                }
            }
        });

        return UserResource::collection($users);
    }

    /**
     * User
     *
     * Returns a user
     *
     * @urlParam user integer required The ID of the user
     *
     * @param string $user
     * @return User
     */
    public function getUser(string $user, Game $game=null)
    {
        if (isset($game)) {
            APIService::setCurrentGame($game);
        }

        $foundUser = null;

        if (ctype_digit($user) && $user < PHP_INT_MAX) {
            $foundUser = User::where('id', $user)->firstOrFail();
        } else {
            $foundUser = User::where(DB::raw('LOWER(unique_name)'), Str::lower($user))->firstOrFail();
        }

        $foundUser->loadMissing('blockedUsers');
        if (Auth::hasUser()) {
            $foundUser->loadMissing('followed');
        }

        if ($foundUser->id === $this->userId() || Auth::user()?->hasPermission('manage-users')) {
            $foundUser->append('signable');
        }

        return new UserResource($foundUser);
    }

    /**
     * POST users/{user}
     *
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, User $user)
    {
        $fileSize = Setting::getValue('image_max_file_size') / 1024;

        $passwordRule = APIService::getPasswordRule();
        $canManageUsers = Auth::user()->hasPermission('manage-users');

        $val = $request->validate([
            'name' => 'string|nullable|min:3|max:30',
            'unique_name' => 'alpha_dash|nullable|min:3|max:50',
            'avatar_file' => ['nullable', File::image()->max($fileSize)],
            'custom_color' => 'string|max:7|nullable',
            'bio' => 'string|nullable|max:3000',
            'email' => 'email|nullable|max:255',
            'custom_title' => 'string|nullable|max:100',
            'private_profile' => 'boolean',
            'invisible' => 'boolean',
            'banner_file' => ['nullable', File::image()->max($fileSize)],
            'donation_url' => 'email_or_url|nullable|max:255',
            'show_tag' => 'in:role,supporter_or_role,none|nullable',
            'current_password' => ['nullable', (!$canManageUsers && $user->signable) ? 'required_with:password' : null],
            'password' => ['nullable', $user->signable ? 'required_with:current_password' : null, $passwordRule, 'max:128'],
            'extra.default_mods_sort' => ['nullable', Rule::in([
                'bumped_at',
                'published_at',
                'likes',
                'downloads',
                'views',
                'score',
                'weekly_score',
                'daily_score',
                'random'
            ])],
            'extra.default_mods_view' => ['nullable', Rule::in(['all', 'liked', 'games', 'mods', 'users'])],
            'extra.home_show_last_games' => 'boolean|nullable',
            'extra.home_show_mods' => 'boolean|nullable',
            'extra.home_show_threads' => 'boolean|nullable',
            'extra.game_show_mods' => 'boolean|nullable',
            'extra.game_show_threads' => 'boolean|nullable',
        ]);

        $extra = Arr::pull($val, 'extra');

        if (isset($val['unique_name'])) {
            $val['unique_name'] = Str::lower($val['unique_name']);
        }

        //TODO: Should moderators be able to change email for users? Sorta.
        APIService::nullToEmptyStr($val, 'custom_color', 'bio', 'custom_title', 'donation_url');

        $avatarFile = Arr::pull($val, 'avatar_file');
        APIService::storeImage($avatarFile, 'users/images', $user->avatar, 64, fn($path) => $user->avatar = $path);

        $bannerFile = Arr::pull($val, 'banner_file');
        APIService::storeImage($bannerFile, 'users/images', $user->avatar, 64, fn($path) => $user->banner = $path);

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
        if (isset($email)) {
            $user->setEmail($email);
        }

        $user->update($val);
        if (isset($extra)) {
            $user->extra->update($extra);
        }
        $user->load('extra');
        $user->append('signable');
        $user->refresh();

        return new UserResource($user);
    }

    function setUserRoles(Request $request, User $user) {
        $this->authorize('manageRoles', $user);

        $val = $request->validate([
            'role_ids' => 'array',
            'role_ids.*' => 'integer|min:2|exists:roles,id',
        ]);

        $user->syncRoles(array_map('intval', array_unique(array_filter($val['role_ids'], fn($val) => is_numeric($val)))));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(User $user)
    {
        $user->delete();
    }

    /**
     * Current User
     *
     * Returns the currently authenticated user
     *
     * @authenticated
     * @param Request $request
     * @return void
     */
    public function currentUser(Request $request)
    {
        $user = $request->user();
        $user->append('signable');
        $user->load('extra');

        return new UserResource($user);
    }

    /**
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
     * Reports the resource for moderators to look at.
     */
    public function report(Request $request, User $user)
    {
        APIService::report($request, $user);
    }

    public function deleteMods(User $user)
    {
        $this->authorize('manageMods', $user);

        foreach ($user->mods as $user) {
            $user->delete();
        }
    }

    public function deleteDiscussions(User $user)
    {
        $this->authorize('manageDiscussions', $user);

        foreach ($user->threads as $thread) {
            $thread->delete();
        }

        foreach ($user->comments as $comment) {
            $comment->delete();
        }
    }

    /**
     * Verifies email via a link sent to the email
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
     */
    public function resendEmail(Request $request)
    {
        $request->user()->sendEmailVerification();
    }

    /**
     * Cancels pending email if the user changes their mind.
     */
    public function cancelPendingEmail()
    {
        $this->user()->forceFill(['pending_email' => null, 'pending_email_set_at' => null])->save();
    }
}
