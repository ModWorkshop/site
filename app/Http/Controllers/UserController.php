<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

/**
 * @group User
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
        $users = User::queryGet($request->validated());
        return UserResource::collection($users);
    }

    /**
     * User
     * 
     * Returns a user
     * 
     * @urlParam user integer required The ID of the user
     *
     * @param User $user
     * @return User
     */
    public function show(User $user)
    {
        $user->load('extra');
        return new UserResource($user);
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
            'avatar_file' => 'nullable|max:512000|mimes:png,webp,gif,jpg',
            'role_ids' => 'array',
            'role_ids.*' => 'integer|min:1',
        ]);

        $valExtra = $request->validate([
            'bio' => 'string|nullable|max:3000',
            'custom_title' => 'string|nullable|max:100',
            'private_profile' => 'required|boolean',
            'banner_file' => 'nullable|max:512000|mimes:png,webp,gif,jpg',
        ]);

        $avatarFile = Arr::pull($val, 'avatar_file');
        if (isset($avatarFile)) {
            if (isset($user->avatar) && !str_contains($user->avatar, 'http')) {
                $oldAvatar = preg_replace('/\?t=\d+/', '', $user->avatar);
                Storage::disk('public')->delete($oldAvatar); // Delete old avatar before uploading
            }
            $path = $avatarFile->storePubliclyAs('avatars', $user->id.'.'.$avatarFile->extension(), 'public');
            $user->avatar = $path.'?t='.time();
        }

        $bannerFile = Arr::pull($valExtra, 'banner_file');
        if (isset($bannerFile)) {
            $oldBanner  = preg_replace('/\?t=\d+/', '', $user->extra->banner);
            if (isset($oldBanner)) {
                Storage::disk('public')->delete($oldBanner); // Delete old avatar before uploading
            }
            $path = $bannerFile->storePubliclyAs('banners', $user->id.'.'.$bannerFile->extension(), 'public');
            $userExtra->banner = $path.'?t='.time();
        }

        //Get all roles first
        $roles = Arr::pull($val, 'role_ids');
        $user->syncRoles($roles);
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
        return new UserResource($user);
    }
}
