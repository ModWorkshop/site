<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\GameRole;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class GameRolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(?User $user, Game $game)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param GameRole $gameRole
     * @return Response|bool
     */
    public function view(?User $user, GameRole $gameRole)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user, Game $game)
    {
        return $user->hasPermission('manage-roles', $game);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param GameRole $gameRole
     * @return Response|bool
     */
    public function update(User $user, GameRole $gameRole)
    {
        return $user->hasPermission('manage-roles', $gameRole->game) && $gameRole->canBeEdited();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param GameRole $gameRole
     * @return Response|bool
     */
    public function delete(User $user, GameRole $gameRole)
    {
        return $this->update($user, $gameRole);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param GameRole $gameRole
     * @return Response|bool
     */
    public function restore(User $user, GameRole $gameRole)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param GameRole $gameRole
     * @return Response|bool
     */
    public function forceDelete(User $user, GameRole $gameRole)
    {
        //
    }
}
