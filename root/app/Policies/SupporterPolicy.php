<?php

namespace App\Policies;

use App\Models\Supporter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupporterPolicy
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
     * @param  \App\Models\Supporter  $supporter
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, Supporter $supporter)
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
        return $user->hasPermission('manage-users');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Supporter  $supporter
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Supporter $supporter)
    {
        return $user->hasPermission('manage-users');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Supporter  $supporter
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Supporter $supporter)
    {
        return $user->hasPermission('manage-users');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Supporter  $supporter
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Supporter $supporter)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Supporter  $supporter
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Supporter $supporter)
    {
        //
    }
}
