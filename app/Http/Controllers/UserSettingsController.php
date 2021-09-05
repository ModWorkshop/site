<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UserSettingsController extends Controller
{
    /**
     * Upload user avatar
     *
     * @bodyParam file file required The new avatar of the user
     * @param Request $request
     * @return void
     */
    public function uploadAvatar(Request $request) {
        $file = $request->file('file');
        $user = $request->user();

        $oldAvatar = preg_replace('/\?t=\d+/', '', $user->avatar);
        if (!str_contains($oldAvatar, 'http')) {
            Storage::disk('public')->delete($oldAvatar); // Delete old avatar before uploading
        }

        $path = $file->storePubliclyAs('avatars', $user->id.'.'.$file->extension(), 'public');

        $user->avatar = $path.'?t='.time();
        $user->save();

        return $user->avatar;
    }
}