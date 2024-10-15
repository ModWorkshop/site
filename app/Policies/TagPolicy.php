<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TagPolicy
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
     * @param Tag $tag
     * @return Response|bool
     */
    public function view(?User $user, Tag $tag)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user, Game $game=null)
    {
        return $user->hasPermission('manage-tags', $game);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Tag $tag
     * @return Response|bool
     */
    public function update(User $user, Tag $tag)
    {
        return $user->hasPermission('manage-tags', $tag->game);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Tag $tag
     * @return Response|bool
     */
    public function delete(User $user, Tag $tag)
    {
        return $user->hasPermission('manage-tags', $tag->game);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Tag $tag
     * @return Response|bool
     */
    public function restore(User $user, Tag $tag)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Tag $tag
     * @return Response|bool
     */
    public function forceDelete(User $user, Tag $tag)
    {
        //
    }
}
