<?php

namespace Tests\Feature;

use App\Models\FollowedGame;
use App\Models\Model;
use App\Models\User;
use Tests\FollowBlockResourceTest;

class FollowedGameTest extends FollowBlockResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'followed-games';
    protected bool $isGlobal = true;
    protected bool $hasParent = false;
    protected string $idKey = 'game_id';

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

    public function upsertData(?Model $parent, string $method): array
    {
        return [
            'game_id' => $this->game2->id, // Follow the second test game
        ];
    }
}
