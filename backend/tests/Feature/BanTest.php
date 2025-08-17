<?php

namespace Tests\Feature;

use App\Models\Ban;
use App\Models\Model;
use App\Models\User;
use Tests\AdminResourceTest;

class BanTest extends AdminResourceTest
{
    protected string $parentUrl = 'games';
    protected string $url = 'bans';
    protected bool $isGlobal = false;
    protected bool $hasParent = true;

    public function createDummy(?User $user = null, ?Model $parent = null): ?Ban
    {
        $targetUser = $this->user(); // Create a user to ban

        return Ban::create([
            'user_id' => $targetUser->id,
            'game_id' => $parent->id,
            'reason' => 'Test ban reason',
            'expire_date' => now()->addDays(30),
            'mod_user_id' => $user->id,
            'active' => true,
            'can_appeal' => true,
        ]);
    }

    public function upsertData(?Model $parent, string $method): array
    {
        $targetUser = $this->user(); // Create a user to ban
        if ($method === 'POST') {
            // For ban creation, user_id is required
            return [
                'user_id' => $targetUser->id,
                'reason' => 'Test ban reason for API testing',
                'expire_date' => now()->addDays(30),
                'can_appeal' => true,
            ];
        } else {
            return [
                'reason' => 'Test ban reason for API testing',
                'expire_date' => now()->addDays(7),
                'can_appeal' => true,
            ];
        }
    }

    /**
     * Bans are typically only viewable by admins and moderators for privacy
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403], // Bans should be private
            'unverified' => ['unverified', 'unverified', 403],
            'verified' => ['verified', 'verified', 403],
            'banned' => ['banned', 'banned', 403],
            'game_banned' => ['game_banned', 'game_banned', 403],
            'admin' => ['admin', 'admin', 200], // Only admins can view bans
        ];
    }
}
