<?php

namespace Tests;

use App\Models\User;

/**
 * Base test class for user-created resources (Reports, Follows, Blocks, etc.)
 * These resources can typically be created by regular verified users
 */
abstract class UserResourceTest extends TestResource
{
    use TestOwnershipTrait;
    /**
     * Default scenarios for user-created resources
     * Verified users can create/update/delete their own content
     * Unverified and banned users typically cannot
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403, 403],
            'unverified' => ['unverified', 'unverified', 403, 403], // Usually can't create
            'verified' => ['verified', 'verified', 201, 201], // Can create
            'banned' => ['banned', 'banned', 403, 403], // Banned users can't create
            'game_banned' => ['game_banned', 'game_banned', 201, 403], // Can create globally, not in game
            'admin' => ['admin', 'admin', 201, 201], // Admins can create
        ];
    }

    public static function updateScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 403, 403],
            'verified' => ['verified', 'verified', 200, 200], // Can update own content
            'banned' => ['banned', 'banned', 403, 403],
            'game_banned' => ['game_banned', 'game_banned', 200, 403], // Can update globally, not in game
            'admin' => ['admin', 'admin', 200, 200],
        ];
    }

    public static function deleteScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 200, 200],
            'verified' => ['verified', 'verified', 200, 200], // Can delete own content
            'banned' => ['banned', 'banned', 200, 200],
            'game_banned' => ['game_banned', 'game_banned', 200, 200], // Can delete globally, not in game
            'admin' => ['admin', 'admin', 200, 200],
        ];
    }

    /**
     * Default view scenarios - typically public or restricted to verified users
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 200, 200],
            'verified' => ['verified', 'verified', 200, 200],
            'banned' => ['banned', 'banned', 200, 200],
            'game_banned' => ['game_banned', 'game_banned', 200, 200],
            'admin' => ['admin', 'admin', 200, 200],
        ];
    }
}
