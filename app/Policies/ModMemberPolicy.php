<?php

namespace App\Policies;

use App\Models\Mod;
use App\Models\ModMember;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ModMemberPolicy
{
    use HandlesAuthorization;
    use AuthorizesRequests;

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Mod $mod)
    {
        return $this->authorize('edit', $mod);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ModMember  $modMember
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Mod $mod, ModMember $modMember)
    {
        return $user->id !== $modMember->user_id && $this->authorize('edit', $mod);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ModMember  $modMember
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Mod $mod, ModMember $modMember)
    {
        //We should be able to remove ourselves from members
        return $user->id === $modMember->user_id || $this->authorize('edit', $mod);
    }
    
    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ModMember  $modMember
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ModMember $modMember)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ModMember  $modMember
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ModMember $modMember)
    {
        //
    }
}
