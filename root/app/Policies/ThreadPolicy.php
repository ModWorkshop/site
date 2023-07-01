<?php

namespace App\Policies;

use App\Models\Ban;
use App\Models\Forum;
use App\Models\ForumCategory;
use App\Models\Thread;
use App\Models\User;
use App\Services\APIService;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Log;

class ThreadPolicy
{
    use HandlesAuthorization, AuthorizesRequests;

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
     * @param Thread $thread
     * @return Response|bool
     */
    public function view(?User $user, Thread $thread)
    {
        $forumCateogry = $thread->category;
        if ($forumCateogry?->private_threads) {
            if (!isset($user) || ($thread->user_id !== $user->id && !$user->hasPermission('manage-users'))) {
                return false;
            }
        }

        return !isset($forumCateogry) || $forumCateogry->can_view;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user, Forum $forum)
    {
        //Handled in the controller
        return true;
    }

    //Moved to here so we can grab category and check stuff
    public function store(User $user, Forum $forum, ForumCategory $category=null)
    {
        $game = $forum->game;

        if ($user->hasPermission('manage-discussions', $game)) {
            return true;
        }

        if (isset($category) && !$category->can_post) {
            return false;
        }

        $canAppeal = $user->last_ban?->can_appeal ?? true;
        $canAppealGame = isset($game) ? ($user->getLastGameban($game->id)?->can_appeal ?? true) : false;

        return $user->hasPermission('create-discussions', $game, $category?->banned_can_post && $canAppeal && $canAppealGame);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Thread $thread
     * @return Response|bool
     */
    public function update(User $user, Thread $thread)
    {
        $game = $thread->forum->game;

        if (isset($game)) {
            APIService::setCurrentGame($game);
        }

        if ($user->hasPermission('manage-discussions', $game)) {
            return true;
        }

        $canAppeal = $user->last_ban?->can_appeal ?? true;
        $canAppealGame = isset($game) ? ($user->getLastGameban($game->id)?->can_appeal ?? true) : false;

        $canPost = $user->hasPermission('create-discussions', $game, $thread->category?->banned_can_post && $canAppeal && $canAppealGame);
        return $canPost && $thread->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Thread $thread
     * @return Response|bool
     */
    public function delete(User $user, Thread $thread)
    {
        return $user->hasPermission('manage-discussions', $thread->forum->game) || $thread->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Thread $thread
     * @return Response|bool
     */
    public function restore(User $user, Thread $thread)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Thread $thread
     * @return Response|bool
     */
    public function forceDelete(User $user, Thread $thread)
    {
        //
    }

    public function createComment(User $user, Thread $thread)
    {
        $game = $thread->forum->game;
        if ($user->hasPermission('manage-discussions', $game)) {
            return true;
        }

        if ($thread->user->blockedMe || !$this->authorize('view', $thread)) {
            return false;
        }

        $category = $thread->category;
        $canAppeal = $user->last_ban?->can_appeal ?? true;
        $canAppealGame = isset($game) ? ($user->getLastGameban($game->id)?->can_appeal ?? true) : false;
        $byPassBan = $thread->user_id === $user->id && $category?->banned_can_post && $canAppeal && $canAppealGame;

        return $user->hasPermission('create-discussions', $game, $byPassBan) && (!$thread->locked || ($user->id === $thread->user_id && !$thread->locked_by_mod));
    }

    // Can we report this thread? Of course if we can see it.
    public function report(User $user, Thread $thread)
    {
        return $this->authorize('view', $thread) && $user->hasPermission('create-reports');
    }
}
