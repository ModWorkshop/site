<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\GameRole;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GameRolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(?User $user, Game $game)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GameRole  $gameRole
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, GameRole $gameRole)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Game $game)
    {
        return $user->hasPermission('manage-roles', $game);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GameRole  $gameRole
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, GameRole $gameRole)
    {
        return $user->hasPermission('manage-roles', $gameRole->game) && $gameRole->canBeEdited();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GameRole  $gameRole
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, GameRole $gameRole)
    {
        return $this->update($user, $gameRole);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GameRole  $gameRole
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, GameRole $gameRole)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GameRole  $gameRole
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, GameRole $gameRole)
    {
        //
    }
}
