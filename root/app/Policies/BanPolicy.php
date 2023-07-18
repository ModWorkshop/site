<?php

namespace App\Policies;

use App\Models\Ban;
use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class BanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user, Game $game=null)
    {
        return $user->hasPermission('moderate-users', $game);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Ban $ban
     * @return Response|bool
     */
    public function view(User $user, Ban $ban)
    {
        return $user->hasPermission('moderate-users', $ban->game);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user, Game $game=null)
    {
        return $user->hasPermission('moderate-users', $game);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Ban $ban
     * @return Response|bool
     */
    public function update(User $user, Ban $ban)
    {
        return $user->hasPermission('moderate-users', $ban->game) && $ban->user->canBeEdited();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Ban $ban
     * @return Response|bool
     */
    public function delete(User $user, Ban $ban)
    {
        return $user->hasPermission('moderate-users', $ban->game) && $ban->user->canBeEdited();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Ban $ban
     * @return Response|bool
     */
    public function restore(User $user, Ban $ban)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Ban $ban
     * @return Response|bool
     */
    public function forceDelete(User $user, Ban $ban)
    {
        //
    }
}
