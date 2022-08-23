<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Mod;
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
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(?User $user)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, Comment $comment)
    {
        return $this->authorize('view', $comment->commentable);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('create-comment');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Comment $comment)
    {
        if ($user->hasPermission('edit-comment') || ($user->hasPermission('edit-own-comment') && $comment->user->id === $user->id)) {
            return $this->authorize('view', $comment->commentable);
        }

        //If we are able to edit a mod and we have the delete own mod comment permission, we should be able to delete the comment
        if ($comment->commentable instanceof Mod && $user->hasPermission('delete-own-mod-comment')) {
            if ($this->authorize('update', $comment->commentable)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Comment $comment)
    {
        if ($comment->user->id === $user->id) {
            return true;
        } else {
            if ($comment->commentable instanceof Mod && $this->authorize('update', $comment->commentable)) {
                return $user->hasPermission('delete-own-mod-comment');
            } else {
                return $user->hasPermission('delete-comment');
            }
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Comment $comment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Comment $comment)
    {
        //
    }
}
