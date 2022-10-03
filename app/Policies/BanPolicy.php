<?php

namespace App\Policies;

use App\Models\Ban;
use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Log;

class BanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, Game $game=null)
    {
        return $user->hasPermission('moderate-users', $game);
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
        return $user->hasPermission('moderate-users', $ban->game);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, ?Game $game)
    {
        return $user->hasPermission('moderate-users', $game);
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
        return $user->hasPermission('moderate-users', $ban->game) && $ban->user->canBeEdited();
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
        return $user->hasPermission('moderate-users', $ban->game) && $ban->user->canBeEdited();
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
