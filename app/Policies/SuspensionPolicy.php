<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\Suspension;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Log;

class SuspensionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(?User $user, Game $game=null)
    {
        return $user->hasPermission('manage-mods', $game);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Suspension $suspension
     * @return Response|bool
     */
    public function view(User $user, Suspension $suspension)
    {
        return $user->hasPermission('manage-mods', $suspension->mod->game);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user, Game $game)
    {
        return $user->hasPermission('manage-mods', $game);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Suspension $suspension
     * @return Response|bool
     */
    public function update(User $user, Suspension $suspension)
    {
        return $user->hasPermission('manage-mods', $suspension->mod->game);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Suspension $suspension
     * @return Response|bool
     */
    public function delete(User $user, Suspension $suspension)
    {
        return $user->hasPermission('manage-mods', $suspension->mod->game);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Suspension $suspension
     * @return Response|bool
     */
    public function restore(User $user, Suspension $suspension)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Suspension $suspension
     * @return Response|bool
     */
    public function forceDelete(User $user, Suspension $suspension)
    {
        //
    }
}
