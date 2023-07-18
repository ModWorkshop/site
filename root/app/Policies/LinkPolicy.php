<?php

namespace App\Policies;

use App\Models\File;
use App\Models\Link;
use App\Models\Mod;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Response;

class LinkPolicy
{
    use HandlesAuthorization;
    use AuthorizesRequests;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(?User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param  File  $file
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, Link $link)
    {
        return $this->authorize('view', $link->mod);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Mod $mod)
    {
        if ($mod->links()->count() >= 25) {
            return false;
        }

        return $this->authorize('create', [Mod::class, $mod->game]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param  File  $file
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Link $link)
    {
        return $this->authorize('update', $link->mod);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param  File  $file
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Link $link)
    {
        return $this->authorize('update', $link->mod);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param  File  $file
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Link $link)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param  File  $file
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Link $link)
    {
        //
    }
}
