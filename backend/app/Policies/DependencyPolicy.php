<?php

namespace App\Policies;

use App\Models\Dependency;
use App\Models\InstructsTemplate;
use App\Models\Mod;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Log;

class DependencyPolicy
{
    use HandlesAuthorization;
    use AuthorizesRequests;

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
     * @param Dependency $dependency
     * @return Response|bool
     */
    public function view(?User $user, Dependency $dependency)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user, $dependable)
    {
        return $this->authorize('update', $dependable);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Dependency $dependency
     * @return Response|bool
     */
    public function update(User $user, Dependency $dependency)
    {
        return $this->authorize('update', $dependency->dependable);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Dependency $dependency
     * @return Response|bool
     */
    public function delete(User $user, Dependency $dependency)
    {
        return $this->authorize('update', $dependency->dependable);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Dependency $dependency
     * @return Response|bool
     */
    public function restore(User $user, Dependency $dependency)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Dependency $dependency
     * @return Response|bool
     */
    public function forceDelete(User $user, Dependency $dependency)
    {
        //
    }
}
