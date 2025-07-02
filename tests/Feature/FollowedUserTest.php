<?php

namespace Tests\Feature;

use App\Models\FollowedUser;
use App\Models\Model;
use App\Models\User;
use Tests\PersonalResourceTest;

class FollowedUserTest extends PersonalResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'followed-users';
    protected bool $isGlobal = true;
    protected bool $hasParent = false;

    public function makeParent(): void
    {
        // No parent needed for followed users
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?FollowedUser
    {
        return FollowedUser::create([
            'user_id' => $user->id,
            'follow_user_id' => $this->user()->id,
            'notify' => false,
        ]);
    }

    public function upsertData(?Model $parent): array
    {
        $followedUser = $this->user(); // Create another user to follow
        
        return [
            'follow_user_id' => $followedUser->id,
        ];
    }

    /**
     * Override scenarios - followed games are personal and private
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 405],
            'verified' => ['verified', 'verified', 405],
            'banned' => ['banned', 'banned', 405],
            'game_banned' => ['game_banned', 'game_banned', 405],
            'admin' => ['admin', 'admin', 405],
        ];
    }

    /**
     * Users can manage their own followed games
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 401],
            'unverified' => ['unverified', 'unverified', 200],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200],
            'game_banned' => ['game_banned', 'game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }

    /**
     * Data provider for update scenarios
     */
    public static function updateScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 405],
            'verified' => ['verified', 'verified', 405],
            'banned' => ['banned', 'banned', 405],
            'game_banned' => ['game_banned', 'game_banned', 405],
            'admin' => ['admin', 'admin', 405],
        ];
    }
}
