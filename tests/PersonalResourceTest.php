<?php

namespace Tests;

use App\Models\User;

/**
 * Base test class for personal user-created resources (Followed Mods)
 * These resources can typically be created by regular verified users
 */
abstract class PersonalResourceTest extends TestResource
{
    /**
     * Data provider for create scenarios
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 401],
            'unverified' => ['unverified', 'unverified', 200],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200],
            'game_banned' => ['game_banned', 'game_banned', 200],
            'other_game_banned' => ['other_game_banned', 'other_game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }

    /**
     * Data provider for update scenarios
     */
    public static function updateScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 200],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200],
            'game_banned' => ['game_banned', 'game_banned', 200],
            'other_game_banned' => ['other_game_banned', 'other_game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }

    /**
     * Data provider for delete scenarios
     */
    public static function deleteScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 200],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200],
            'game_banned' => ['game_banned', 'game_banned', 200],
            'other_game_banned' => ['other_game_banned', 'other_game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }

    /**
     * Data provider for view scenarios
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 200],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200],
            'game_banned' => ['game_banned', 'game_banned', 200],
            'other_game_banned' => ['other_game_banned', 'other_game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }
}
