<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
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
    public function index()
    {
        //
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
        $val = $request->validate([
            'name' => 'string|nullable|min:3|max:100',
            'avatar-file' => 'nullable|max:512000|mimes:png,webp,gif',
            'roles' => 'array',
            'roles.*' => 'integer|min:1',
        ]);

        $user = $request->user();

        $oldAvatar = preg_replace('/\?t=\d+/', '', $user->avatar);
        if (!str_contains($oldAvatar, 'http')) {
            Storage::disk('public')->delete($oldAvatar); // Delete old avatar before uploading
        }

        $avatarFile = Arr::pull($val, 'avatar-file');
        if (isset($avatarFile)) {
            $path = $avatarFile->storePubliclyAs('avatars', $user->id.'.'.$avatarFile->extension(), 'public');
            $user->avatar = $path.'?t='.time();
        }

        //Get all roles first
        $roles = Arr::pull($val, 'roles');
        $user->syncRoles($roles);
        $user->update($val);

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
        return new UserResource($request->user());
    }
}
