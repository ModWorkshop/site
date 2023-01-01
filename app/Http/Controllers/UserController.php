<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ModResource;
use App\Http\Resources\ThreadResource;
use App\Http\Resources\UserResource;
use App\Models\Mod;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\APIService;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;
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
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val([
            'id' => 'integer|min:1'
        ]);

        $users = User::queryGet($val, function($query, $val) {
            if (isset($val['id'])) {
                $query->orWhere('id', $val['id']);
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
    public function getUser(string $user)
    {
        $foundUser = null;

        if (is_numeric($user)) {
            $foundUser = User::where('id', $user)->orWhere('unique_name', $user)->firstOrFail();
        } else {
            $foundUser = User::where('unique_name', Str::lower($user))->firstOrFail();
        }

        $foundUser->loadMissing('blockedUsers');
        if (Auth::hasUser()) {
            $foundUser->loadMissing('followed');
        }

        if ($foundUser->id === $this->userId()) {
            $foundUser->append('signable');
        }

        return new UserResource($foundUser);
    }

    /**
     * POST users/{user}
     * 
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $fileSize = Setting::getValue('image_max_file_size') / 1024;

        $passwordRule = APIService::getPasswordRule();

        $val = $request->validate([
            'name' => 'string|nullable|min:3|max:100',
            'unique_name' => 'alpha_dash|nullable|min:3|max:50',
            'avatar_file' => ['nullable', File::image()->max($fileSize)],
            'custom_color' => 'string|max:7|nullable',
            'bio' => 'string|nullable|max:3000',
            'email' => 'email|nullable',
            'custom_title' => 'string|nullable|max:100',
            'private_profile' => 'boolean',
            'invisible' => 'boolean',
            'banner_file' => ['nullable', File::image()->max($fileSize)],
            'donation_url' => 'email_or_url|nullable|max:255',
            'show_tag' => 'in:role,supporter_or_role,none|nullable',
            'current_password' => ['nullable', $user->signable ? 'required_with:password' : null, $passwordRule],
            'password' => ['nullable', $user->signable ? 'required_with:current_password' : null, $passwordRule],
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
            $val['unique_name'] = strtolower($val['unique_name']);
        }

        //TODO: Should moderators be able to change email for users?
        APIService::nullToEmptyStr($val, 'custom_color', 'bio', 'custom_title', 'donation_url');

        $avatarFile = Arr::pull($val, 'avatar_file');
        APIService::tryUploadFile($avatarFile, 'users/avatars', $user->avatar, fn($path) => $user->avatar = $path);

        $bannerFile = Arr::pull($val, 'banner_file');
        APIService::tryUploadFile($bannerFile, 'users/banners', $user->avatar, fn($path) => $userExtra->banner = $path);

        //Change password code
        $password = Arr::pull($val, 'password');
        if (isset($password)) {
            //We must require current password, but remember that there are accounts that sign in using SSO.
            if (!isset($user->password) || Hash::check(Arr::pull($val, 'current_password'), $user->password)) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
            } else {
                abort(422, 'Current password is incorrect');
            }
        }

        $user->update($val);
        $user->extra->update($extra);
        $user->load('extra');

        return new UserResource($user);
    }

    function setUserRoles(Request $request, User $user) {
        $this->authorize('manageRoles', $user);

        $val = $request->validate([
            'role_ids' => 'array',
            'role_ids.*' => 'integer|min:2|exists:roles,id',
        ]);

        $user->syncRoles(array_map('intval', array_filter($val['role_ids'], fn($val) => is_numeric($val))));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
}
