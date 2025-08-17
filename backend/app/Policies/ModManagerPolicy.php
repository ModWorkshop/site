<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\ModManager;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ModManagerPolicy
{
    use HandlesAuthorization;
    use AuthorizesRequests;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, ModManager $modManager): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Game $game=null): bool
    {
        return $user->hasPermission('manage-mods', $game);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ModManager $modManager): bool
    {
        return $user->hasPermission('manage-mods', $modManager->game);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ModManager $modManager): bool
    {
        return $this->update($user, $modManager);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ModManager $modManager): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ModManager $modManager): bool
    {
        //
    }
}
