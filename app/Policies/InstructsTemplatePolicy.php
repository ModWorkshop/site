<?php

namespace App\Policies;

use App\Models\InstructsTemplate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstructsTemplatePolicy
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
     * @param  \App\Models\InstructsTemplate  $InstructsTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, InstructsTemplate $InstructsTemplate)
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
        return $user->hasPermission('manage_instructions');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\InstructsTemplate  $InstructsTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, InstructsTemplate $InstructsTemplate)
    {
        return $user->hasPermission('manage_instructions');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\InstructsTemplate  $InstructsTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, InstructsTemplate $InstructsTemplate)
    {
        return $user->hasPermission('manage_instructions');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\InstructsTemplate  $InstructsTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, InstructsTemplate $InstructsTemplate)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\InstructsTemplate  $InstructsTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, InstructsTemplate $InstructsTemplate)
    {
        //
    }
}
