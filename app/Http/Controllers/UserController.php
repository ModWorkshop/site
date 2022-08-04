<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\APIService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
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

        $users = User::queryGet($val, function(Builder $query) use ($val) {
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

        $foundUser->load('extra');
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

        $val = $request->validate([
            'name' => 'string|nullable|min:3|max:100',
            'unique_name' => 'string|nullable|min:3|max:50',
            'avatar_file' => 'nullable|max:512000|mimes:png,webp,gif,jpg',
            'role_ids' => 'array',
            'role_ids.*' => 'integer|min:1',
            'custom_color' => 'string|max:7|nullable'
        ]);

        $val['custom_color'] ??= '';

        $valExtra = $request->validate([
            'bio' => 'string|nullable|max:3000',
            'custom_title' => 'string|nullable|max:100',
            'private_profile' => 'required|boolean',
            'banner_file' => 'nullable|max:512000|mimes:png,webp,gif,jpg',
            'donation_url' => 'email_or_url|nullable|max:255',
        ]);

        $valExtra['bio'] ??= '';
        $valExtra['custom_title'] ??= '';
        $valExtra['donation_url'] ??= '';

        $avatarFile = Arr::pull($val, 'avatar_file');
        APIService::tryUploadFile($avatarFile, 'users/avatars', $user->avatar, fn($path) => $user->avatar = $path);

        $bannerFile = Arr::pull($valExtra, 'banner_file');
        APIService::tryUploadFile($bannerFile, 'users/banners', $user->avatar, fn($path) => $userExtra->banner = $path);

        //Get all roles first
        $roles = Arr::pull($val, 'role_ids');
        if (isset($roles)) {
            $user->syncRoles($roles);
        }
        $user->update($val);
        $userExtra->update($valExtra);

        return new UserResource($user);
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
        $user->load('extra');
        $user->load('roles.permissions');
        $user->extra;
        return new UserResource($user);
    }
}
