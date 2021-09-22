<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * @group Users
 */
class UserController extends Controller
{
    /**
     * User
     * 
     * Returns a user
     * 
     * @urlParam user integer required The ID of the user
     *
     * @param User $user
     * @return void
     */
    public function getUser(User $user)
    {
        return $user->toJson();
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
        //Since this is the current user, it's safe to just return their email to themselves
        //Note that this does handle admins wanting to get user's emails.
        return $request->user()->makeVisible('email');
    }
}
