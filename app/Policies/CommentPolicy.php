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
        return true; //$user->hasPermission('create-discussions'); Handled by createComment policy
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
        $commentable = $comment->commentable;
        if ($user->hasPermission('manage-discussions', $commentable->game) || ($user->hasPermission('create-discussions', $commentable->game) && $comment->user->id === $user->id)) {
            return $this->authorize('view', $commentable);
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
