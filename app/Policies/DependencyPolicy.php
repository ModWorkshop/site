<?php

namespace App\Policies;

use App\Models\Dependency;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DependencyPolicy
{
    use HandlesAuthorization;
    use AuthorizesRequests;

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
     * @param  \App\Models\Dependency  $dependency
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, Dependency $dependency)
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
        return $this->authorize('create', Mod::class);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Dependency  $dependency
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Dependency $dependency)
    {
        return $this->authorize('update', $dependency->dependable); 
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Dependency  $dependency
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Dependency $dependency)
    {
        return $this->authorize('update', $dependency->dependable); 
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Dependency  $dependency
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Dependency $dependency)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Dependency  $dependency
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Dependency $dependency)
    {
        //
    }
}
