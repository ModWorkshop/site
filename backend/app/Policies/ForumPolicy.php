<?php

namespace App\Policies;

use App\Models\Forum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ForumPolicy
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
     * @param Forum $forum
     * @return Response|bool
     */
    public function view(?User $user, Forum $forum)
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
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Forum $forum
     * @return Response|bool
     */
    public function update(User $user, Forum $forum)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Forum $forum
     * @return Response|bool
     */
    public function delete(User $user, Forum $forum)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Forum $forum
     * @return Response|bool
     */
    public function restore(User $user, Forum $forum)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Forum $forum
     * @return Response|bool
     */
    public function forceDelete(User $user, Forum $forum)
    {
        //
    }
}
