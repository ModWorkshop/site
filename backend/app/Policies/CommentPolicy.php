<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Mod;
use App\Models\Setting;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\Response;

class CommentPolicy
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
     * @param Comment $comment
     * @return Response|bool
     */
    public function view(?User $user, Comment $comment)
    {
        return $this->authorize('view', $comment->commentable);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        return true; //$user->hasPermission('create-discussions'); Handled by createComment policy
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Comment $comment
     * @return Response|bool
     */
    public function update(User $user, Comment $comment)
    {
        $commentable = $comment->commentable;

        if ($user->hasPermission('manage-discussions', $commentable->game) || ($user->hasPermission('create-discussions', $commentable->game) && $comment->user->id === $user->id)) {
            /**
             * If you are able to edit the thread or mod, don't limit editing
             */
            $threshHold = Setting::getValue('edit_comment_threshold');
            if ($comment->created_at->diff()->minutes >= $threshHold) {
                return $this->authorize('update', $commentable);
            } else {
                return $this->authorize('view', $commentable);
            }
        }


        return false;
    }

    /**
     * Determine whether the user can pin the comment.
     */
    public function pin(User $user, Comment $comment) {
        $commentable = $comment->commentable;
        // Either you can manage discussions or edit the resource that the comment belongs to (mod, thread)
        return $user->hasPermission('manage-discussions', $commentable->game) || $this->authorize('update', $commentable);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Comment $comment
     * @return Response|bool
     */
    public function delete(User $user, Comment $comment)
    {
        if ($comment->user->id === $user->id) {
            return true;
        } else {
            $commentable = $comment->commentable;
            if ($commentable instanceof Mod && $this->authorize('update', $commentable)) {
                return $user->hasPermission('delete-own-mod-comments', $commentable->game);
            } else {
                return $user->hasPermission('manage-discussions', $commentable->game);
            }
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Comment $comment
     * @return Response|bool
     */
    public function restore(User $user, Comment $comment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Comment $comment
     * @return Response|bool
     */
    public function forceDelete(User $user, Comment $comment)
    {
        //
    }

    // Can we report this comment? If we can see also it
    public function report(User $user, Comment $comment)
    {
        return $this->authorize('view', $comment) && $user->hasPermission('create-reports');
    }
}
