<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Returns whether or not you are allowed to view this user's email
     * Generally, no user should be able to view emails. However, we make an exception for admins.
     *
     * @param User $user
     * @param User $userToCheck
     * @return void
     */
    public function viewEmail(User $user, User $userToCheck)
    {
        return ($user->id === $userToCheck->id || $user->hasPermission('admin')) ? Response::allow() : Response::deny("You cannot look at this user's email!");
    }
}
