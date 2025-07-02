<?php

namespace Tests\Feature;

use App\Models\Forum;
use App\Models\Model;
use App\Models\User;
use Tests\AdminResourceTest;

class ForumTest extends AdminResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'forums';
    protected bool $isGlobal = true;
    protected bool $hasParent = false;

    public function makeParent(): void
    {

    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?Forum
    {
        return Forum::create([
            'name' => 'Test Forum',
            'game_id' => $this->game->id
        ]);
    }

    public function upsertData(?Model $parent, string $method): array
    {
        return [
            'name' => 'Test Forum Name',
            'game_id' => $this->game->id
        ];
    }
    
    public static function updateScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 405],
            'unverified' => ['unverified', 'unverified', 405],
            'verified' => ['verified', 'verified', 405],
            'banned' => ['banned', 'banned', 405],
            'game_banned' => ['game_banned', 'game_banned', 405],
            'admin' => ['admin', 'admin', 405], // Forums might not be creatable via API
        ];
    }

    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 405],
            'unverified' => ['unverified', 'unverified', 405],
            'verified' => ['verified', 'verified', 405],
            'banned' => ['banned', 'banned', 405],
            'game_banned' => ['game_banned', 'game_banned', 405],
            'admin' => ['admin', 'admin', 405], // Forums might not be creatable via API
        ];
    }

    public static function deleteScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 405],
            'unverified' => ['unverified', 'unverified', 405],
            'verified' => ['verified', 'verified', 405],
            'banned' => ['banned', 'banned', 405],
            'game_banned' => ['game_banned', 'game_banned', 405],
            'admin' => ['admin', 'admin', 405], // Forums might not be deletable via API
        ];
    }
}
