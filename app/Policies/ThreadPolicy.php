<?php

namespace App\Policies;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadPolicy
{
    use HandlesAuthorization;

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
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('create-threads');
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
        return $user->hasPermission('update-threads') || ($user->hasPermission('create-threads') && $thread->user_id === $user->id);
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
        return $this->update($user, $thread);
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
        return !$thread->archived_at || $user->hasPermission('update-threads');
    }
}
