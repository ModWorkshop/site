<?php

namespace App\Policies;

use App\Models\Forum;
use App\Models\ForumCategory;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ThreadPolicy
{
    use HandlesAuthorization, AuthorizesRequests;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(?User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, Thread $thread)
    {
        $forumCateogry = $thread->category;

        if ($forumCateogry->private_threads) {
            if (!(isset($user) || $thread->user_id === $user->id || $user->hasPermission('manage-users'))) {
                return false;
            }
        }

        return !isset($forumCateogry) || $forumCateogry->can_view;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Forum $forum)
    {
        //Handled in the controller
        return true;
    }

    //Moved to here so we can grab category and check stuff
    public function store(User $user, Forum $forum, ForumCategory $category)
    {
        $game = $forum->game;

        if ($user->hasPermission('manage-discussions', $game)) {
            return true;
        }

        if (isset($category) && !$category->can_post) {
            return false;
        }

        $canAppeal = $user->last_ban?->can_appeal ?? true;
        $canAppealGame = $user->last_game_ban?->can_appeal ?? true;

        return $user->hasPermission('create-discussions', $game, $category->banned_can_post && $canAppeal && $canAppealGame);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Thread $thread)
    {
        $game = $thread->forum->game;

        if ($user->hasPermission('manage-discussions', $game)) {
            return true;
        }
        
        $canAppeal = $user->last_ban?->can_appeal ?? true;
        $canAppealGame = $user->last_game_ban?->can_appeal ?? true;

        $canPost = $user->hasPermission('create-discussions', $game, $thread->category?->banned_can_post && $canAppeal && $canAppealGame);
        return $canPost && $thread->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Thread $thread)
    {
        return $user->hasPermission('manage-discussions', $thread->forum->game) || $thread->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Thread $thread)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Thread $thread)
    {
        //
    }

    public function createComment(User $user, Thread $thread)
    {
        if ($user->hasPermission('manage-discussions')) {
            return true;
        }

        if ($thread->user->blockedMe || !$this->authorize('view', $thread)) {
            return false;
        }

        return !$thread->locked || ($user->id === $thread->user_id && !$thread->locked_by_mod);
    }
}
