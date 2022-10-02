<?php

namespace App\Services;

use App\Models\Game;
use App\Models\GameRole;
use App\Models\Role;
use Auth;

class RoleService {
    /**
     * Returns whether or not the order of the currently authenticated user is able to edit the given order
     */
    public static function canEdit(int $order): bool
    {
        $user = Auth::user();
        
        if (!isset($user)) {
            return false;
        }

        return $user->highestRoleOrder > $order;
    }

    /**
     * Returns whether or not the order of the currently authenticated user is able to edit the given order of a game role
     */
    public static function canEditGameRole(Game $game, int $order): bool
    {
        $user = Auth::user();
        
        if (!isset($user)) {
            return false;
        }

        return $user->getGameHighestOrder($game) > $order;
    }

    /**
     * Reorders roles to ensure they follow proper order.
     * Separated by 2 so we can move them around easily.
     */
    public static function reorderRoles()
    {
        Role::flushQueryCache();

        $roles = Role::whereNotNull('order')->orderByDesc('order')->get();

        $nextOrder = 1000;

        foreach ($roles as $role) {
            $role->update(['order' => $nextOrder]);
            $nextOrder -= 2;
        }
    }

    /**
     * Reorders roles to ensure they follow proper order.
     * Separated by 2 so we can move them around easily.
     */
    public static function reordrGameRoles(Game $game)
    {
        GameRole::flushQueryCache();

        $roles = GameRole::where('game_id', $game->id)->orderByDesc('order')->get();

        $nextOrder = 1000;

        foreach ($roles as $role) {
            $role->update(['order' => $nextOrder]);
            $nextOrder -= 2;
        }
    }
}