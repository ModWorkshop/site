<?php

namespace App\Policies;

use App\Models\Mod;
use App\Models\User;
use App\Models\Visibility;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ModPolicy
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
     * @param  \App\Models\Mod  $mod
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, Mod $mod)
    {
        switch ($mod->visibility) {
            case Visibility::unlisted:
            case Visibility::pub:
                return !$mod->suspended;
            case Visibility::hidden:
                return $mod->submitter->id === $user?->id; //TODO: account for collaborators & Invitees
        }
        return false;
    }

    public function like(User $user, Mod $mod)
    {
        //TODO: handle bans and such
        return $user->id !== $mod->submitter_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('edit-mod');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mod  $mod
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Mod $mod)
    {
        return $user->id === $mod->submitter_id || $user->hasPermission('edit-all-mod');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mod  $mod
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Mod $mod)
    {
        return $this->update($user, $mod); //TODO: && Only maintainer level contributor should be allowed to delete
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mod  $mod
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Mod $mod)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mod  $mod
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Mod $mod)
    {
        //
    }
}
