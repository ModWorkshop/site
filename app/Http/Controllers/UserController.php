<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\UserResource;
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
        $userExtra = $user->extra;
        $fileSize = Setting::getValue('image_max_file_size') / 1024;

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
            'current_password' => ['nullable', $user->signable ? 'required_with:password' : null, Password::min(12)->numbers()->mixedCase()->uncompromised()],
            'password' => ['nullable', $user->signable ? 'required_with:current_password' : null, Password::min(12)->numbers()->mixedCase()->uncompromised()],
        ]);

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

        return new UserResource($user);
    }

    /**
     * Reports the resource for moderators to look at.
     */
    public function report(Request $request, User $user)
    {
        APIService::report($request, $user);
    }
}
