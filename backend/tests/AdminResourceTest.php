<?php

namespace Tests;

use App\Models\User;

/**
 * Base test class for admin-only resources (Games, Categories, Forums, Roles, etc.)
 * These resources typically require special permissions like manage-games, manage-categories, etc.
 */
abstract class AdminResourceTest extends TestResource
{
    protected function testScenario(string $operation, $data) {
        $data['owner'] ??= $this->admin(); // Default owner is admin for admin resources
        return parent::testScenario($operation, $data);
    }

    /**
     * Default scenarios for admin-only resources
     * Only admins (or users with specific permissions) can create/update/delete
     * But viewing is typically public or has different rules
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403, 403],
            'unverified' => ['unverified', 'unverified', 403, 403],
            'verified' => ['verified', 'verified', 403, 403],
            'banned' => ['banned', 'banned', 403, 403],
            'game_banned' => ['game_banned', 'game_banned', 403, 403],
            'admin' => ['admin', 'admin', 201, 201], // Only admins can create
        ];
    }

    public static function updateScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403, 403],
            'unverified' => ['unverified', 'unverified', 403, 403],
            'verified' => ['verified', 'verified', 403, 403],
            'banned' => ['banned', 'banned', 403, 403],
            'game_banned' => ['game_banned', 'game_banned', 403, 403],
            'admin' => ['admin', 'admin', 200, 200], // Only admins can update
        ];
    }

    public static function deleteScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403, 403],
            'unverified' => ['unverified', 'unverified', 403, 403],
            'verified' => ['verified', 'verified', 403, 403],
            'banned' => ['banned', 'banned', 403, 403],
            'game_banned' => ['game_banned', 'game_banned', 403, 403],
            'admin' => ['admin', 'admin', 200, 200], // Only admins can delete
        ];
    }

    /**
     * Default view scenarios - typically public for most admin resources
     * Child classes can override this if they have different view permissions
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 200, 200], // Most admin resources are publicly viewable
            'unverified' => ['unverified', 'unverified', 200, 200],
            'verified' => ['verified', 'verified', 200, 200],
            'banned' => ['banned', 'banned', 200, 200],
            'game_banned' => ['game_banned', 'game_banned', 200, 200],
            'admin' => ['admin', 'admin', 200, 200],
        ];
    }
}
