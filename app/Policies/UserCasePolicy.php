<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;
use App\Models\UserCase;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserCasePolicy
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
     * @param UserCase $userCase
     * @return Response|bool
     */
    public function view(User $user, UserCase $userCase)
    {
        return $user->hasPermission('moderate-users', $userCase->game);
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
     * @param UserCase $userCase
     * @return Response|bool
     */
    public function update(User $user, UserCase $userCase)
    {
        return $user->hasPermission('moderate-users', $userCase->game);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param UserCase $userCase
     * @return Response|bool
     */
    public function delete(User $user, UserCase $userCase)
    {
        return $user->hasPermission('moderate-users', $userCase->game);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param UserCase $userCase
     * @return Response|bool
     */
    public function restore(User $user, UserCase $userCase)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param UserCase $userCase
     * @return Response|bool
     */
    public function forceDelete(User $user, UserCase $userCase)
    {
        //
    }
}
