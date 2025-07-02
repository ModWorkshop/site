<?php

namespace Tests\Feature;

use App\Models\FollowedGame;
use App\Models\Model;
use App\Models\User;
use Tests\PersonalResourceTest;

class FollowedGameTest extends PersonalResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'followed-games';
    protected bool $isGlobal = true;
    protected bool $hasParent = false;

    public function makeParent(): void
    {
        // No parent needed for followed games
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?FollowedGame
    {        
        return FollowedGame::create([
            'user_id' => $user->id,
            'game_id' => $this->game->id, // Follow the test game
        ]);
    }

    public function upsertData(?Model $parent): array
    {
        return [
            'game_id' => $this->game2->id, // Follow the second test game
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
