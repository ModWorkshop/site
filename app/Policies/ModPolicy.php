<?php

namespace App\Policies;

use App\Models\Mod;
use App\Models\User;
use App\Models\Visibility;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ModPolicy
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
        if (!isset($user) || !$this->update($user, $mod)) {
            if ($mod->suspended) {
                return Response::deny('suspended');
            }

            if ($mod->approved === null) {
                return Response::deny('unapproved');
            }

            if ($mod->approved === false) {
                return Response::deny('rejected');
            }
        }

        switch ($mod->visibility) {
            case Visibility::unlisted:
            case Visibility::pub:
                return true;
            case Visibility::hidden:
                if (!isset($user)) {
                    return false;
                }
                return $this->update($user, $mod);
        }
        return false;
    }

    public function like(User $user, Mod $mod)
    {
        return $user->id !== $mod->user_id && $this->view($user, $mod) && $user->hasPermission('like-mods');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('create-mods');
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
        if ($user->hasPermission('manage-mods')) {
            return true;
        }

        if (!$user->hasPermission('create-mods')) {
            return false;
        }

        if ($user->id === $mod->user_id) {
            return true;
        }

        $ourLevel = $mod->getMemberLevel($user->id);
        return ($ourLevel === 1 || $ourLevel === 0); //Maintainer or Collaborator
    }

    /**
     * Essentially the highest permission for a mod; owner or moderator.
     *
     * @param User $user
     * @param Mod $mod
     * @return void
     */
    public function superUpdate(User $user, Mod $mod)
    {
        return $user->id === $mod->user_id || $user->hasPermission('manage-mods');
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
        if ($user->hasPermission('manage-mods')) {
            return true;
        }

        //Users should be able to delete their own mods unconditionally.
        if ($user->id === $mod->user_id) {
            return true;
        }

        //Maintainers shouldn't be able to do anything if they can't create mods.
        if (!$user->hasPermission('create-mods')) {
            return false;
        }

        return $mod->getMemberLevel($user->id) === 0; //Maintainer
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

    public function createComment(User $user, Mod $mod)
    {
        if (!$user->hasPermission('create-discussions') || $mod->user->blockedMe) {
            return false;
        }

        if ($mod->comments_disabled) {
            return $this->update($user, $mod);
        } else {
            return $this->view($user, $mod);
        }
    }

    public function manage(User $user)
    {
        return $user->hasPermission('manage-mods');
    }

    public function report(User $user, Mod $mod)
    {
        return !$mod->suspended && $user->hasPermission('create-reports');
    }
}
