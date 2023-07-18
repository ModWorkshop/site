<?php

namespace App\Policies;

use App\Models\ForumCategory;
use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ForumCategoryPolicy
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
     * @param ForumCategory $forumCategory
     * @return Response|bool
     */
    public function view(?User $user, ForumCategory $forumCategory)
    {
        return $forumCategory->can_view;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user, Game $game=null)
    {
        return $user->hasPermission('manage-forum-categories', $game);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param ForumCategory $forumCategory
     * @return Response|bool
     */
    public function update(User $user, ForumCategory $forumCategory)
    {
        return $user->hasPermission('manage-forum-categories', $forumCategory->forum->game);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param ForumCategory $forumCategory
     * @return Response|bool
     */
    public function delete(User $user, ForumCategory $forumCategory)
    {
        return $user->hasPermission('manage-forum-categories', $forumCategory->forum->game);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param ForumCategory $forumCategory
     * @return Response|bool
     */
    public function restore(User $user, ForumCategory $forumCategory)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param ForumCategory $forumCategory
     * @return Response|bool
     */
    public function forceDelete(User $user, ForumCategory $forumCategory)
    {
        //
    }
}
