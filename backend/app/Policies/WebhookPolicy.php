<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Telescope\AuthorizesRequests;

class WebhookPolicy
{
    use HandlesAuthorization, AuthorizesRequests;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, ?Game $game=null): bool
    {
        return $user->hasPermission('manage-webhooks', $game);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Webhook $webhook): bool
    {
        return $user->hasPermission('manage-webhooks', $webhook->game);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, ?Game $game=null): bool
    {
        return $user->hasPermission('manage-webhooks', $game);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Webhook $webhook): bool
    {
        return $user->hasPermission('manage-webhooks', $webhook->game);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Webhook $webhook): bool
    {
        return $user->hasPermission('manage-webhooks', $webhook->game);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Webhook $webhook): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Webhook $webhook): bool
    {
        return false;
    }
}
