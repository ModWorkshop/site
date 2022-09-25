<?php

namespace App\Policies;

use App\Models\Ban;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('moderate-users');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ban  $ban
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Ban $ban)
    {
        return $user->hasPermission('moderate-users');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('moderate-users');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ban  $ban
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Ban $ban)
    {
        return $user->hasPermission('moderate-users') && $ban->user->canBeEdited($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ban  $ban
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Ban $ban)
    {
        return $user->hasPermission('moderate-users') && $ban->user->canBeEdited($user);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ban  $ban
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Ban $ban)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ban  $ban
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Ban $ban)
    {
        //
    }
}
