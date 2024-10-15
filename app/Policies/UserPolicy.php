<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(?User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function view(?User $user, User $model)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function update(User $user, User $model)
    {
        return $user->id === $model->id || $model->canBeEdited();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function delete(User $user, User $model)
    {
        return $user->id === $model->id || $user->hasPermission('admin');
    }

    public function manageRoles(User $user, User $model)
    {
        return $user->id === $model->id || $user->hasPermission('manage-roles');
    }

    public function manageDiscussions(User $user, User $model)
    {
        return $user->id === $model->id || $user->hasPermission('manage-discussions');
    }

    public function purge(User $user, User $model)
    {
        return $user->hasPermission('manage-users') && $this->update($user, $model);
    }

    public function manageMods(User $user, User $model)
    {
        return $user->id === $model->id || $user->hasPermission('manage-mods');
    }

    public function viewDiscussions(?User $user, User $model) {
        return $user->id === $model->id || $user->hasPermission('manage-discussions');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }

    // Can we report this user? Currently nothing special
    public function report(User $user, User $model)
    {
        return $user->hasPermission('create-reports');
    }
}
