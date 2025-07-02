<?php

namespace Tests\Feature;

use App\Models\BlockedUser;
use App\Models\Model;
use App\Models\User;
use Tests\PersonalResourceTest;

class BlockedUserTest extends PersonalResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'blocked-users';
    protected bool $isGlobal = true;
    protected bool $hasParent = false;

    public function makeParent(): void
    {
        // No parent needed for blocked users
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?BlockedUser
    {
        return BlockedUser::create([
            'user_id' => $user->id,
            'block_user_id' => $this->user()->id,
            'silent' => false,
        ]);
    }

    public function upsertData(?Model $parent): array
    {
        $blockedUser = $this->user(); // Create another user to block
        
        return [
            'user_id' => $blockedUser->id,
            'silent' => true,
        ];
    }

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
}
