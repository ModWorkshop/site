<?php

namespace Tests;

use App\Models\Model;
use Illuminate\Testing\TestResponse;

/**
 * Base test class for personal user-created resources (Followed Mods)
 * These resources can typically be created by regular verified users
 */
abstract class FollowBlockResourceTest extends TestResource
{
    /**
     * Users can only manage their own blocked users
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 401], // Must be authenticated
            'unverified' => ['unverified', 'unverified', 200], // Can block users
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200], // Even banned users can block others
            'game_banned' => ['game_banned', 'game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }

    public static function viewScenariosProvider(): array
    {
        return [
            'verified' => ['verified', 'verified', 405],
        ];
    }

    public static function updateScenariosProvider(): array
    {
        return [
            'verified' => ['verified', 'verified', 405],
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

    protected function assertPostOperationResult(string $httpMethod, TestResponse $rs, int $assertStatus, array $requestData, ?Model $resource, ?Model $parent): void
    {
        if (in_array($assertStatus, [200, 201])) {
            // For POST, we need to find the created resource in the database
            $modelClass = $this->getModelClass();
            if ($modelClass) {
                // Try to find the most recently created resource that matches our data
                $createdResource = $modelClass::where($this->idKey, $requestData[$this->idKey])->first();
                // $rs->assertNoContent(); // This should be the case maybe, but we don't actually return 204 when it's empty...
                $this->assertNotNull($createdResource, 'Resource should exist after POST operation');
            }
        }
    }
}
