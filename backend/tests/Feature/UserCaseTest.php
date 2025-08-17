<?php

namespace Tests\Feature;

use App\Models\Model;
use App\Models\UserCase;
use App\Models\User;
use Tests\AdminResourceTest;

class UserCaseTest extends AdminResourceTest
{
    protected string $parentUrl = 'games';
    protected string $url = 'user-cases';
    protected bool $isGlobal = true;
    protected bool $hasParent = true;

    public function createDummy(?User $user = null, ?Model $parent = null): ?UserCase
    {
        $targetUser = $this->user(); // Create a user for the case

        return UserCase::create([
            'user_id' => $targetUser->id,
            'mod_user_id' => $user->id,
            'game_id' => $parent?->id,
            'reason' => 'Test user case reason',
            'expire_date' => now()->addDays(30),
            'active' => true
        ]);
    }

    public function upsertData(?Model $parent, string $method): array
    {
        $targetUser = $this->user(); // Create a user for the case
        
        if ($method === 'POST') {
            // For case creation, user_id is required
            return [
                'user_id' => $targetUser->id,
                'reason' => 'Test user case reason for API testing',
                'expire_date' => now()->addDays(30),
                'active' => true,
            ];
        } else {
            // For case updates, user_id is not required
            // Use the existing user_id from the dummy case
            return [
                'reason' => 'Updated test user case reason for API testing',
                'expire_date' => now()->addDays(14),
                'active' => true,
            ];
        }
    }

    /**
     * User cases are typically only viewable by admins and moderators for privacy
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403], // User cases should be private
            'unverified' => ['unverified', 'unverified', 403],
            'verified' => ['verified', 'verified', 403],
            'banned' => ['banned', 'banned', 403],
            'game_banned' => ['game_banned', 'game_banned', 403],
            'admin' => ['admin', 'admin', 200], // Only admins can view user cases
        ];
    }

    /**
     * Only admins can create user cases (warnings, notes, etc.)
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403, 403],
            'unverified' => ['unverified', 'unverified', 403, 403],
            'verified' => ['verified', 'verified', 403, 403], // Regular users can't create cases
            'banned' => ['banned', 'banned', 403, 403],
            'game_banned' => ['game_banned', 'game_banned', 403, 403],
            'admin' => ['admin', 'admin', 201, 201], // Only admins can create user cases
        ];
    }
}
