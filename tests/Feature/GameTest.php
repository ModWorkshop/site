<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Model;
use App\Models\User;
use Tests\AdminResourceTest;

class GameTest extends AdminResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'games';
    protected bool $isGlobal = true;
    protected bool $hasParent = false;

    public function makeParent(): void
    {

    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?Game
    {
        return Game::create([
            'name' => 'Test Game',
            'short_name' => 'testgame',
            'thumbnail' => 'default.png',
            'banner' => 'default.png', 
            'buttons' => 'Test buttons',
            'webhook_url' => '',
            'last_date' => now(),
        ]);
    }

    public function upsertData(?Model $parent): array
    {
        return [
            'name' => 'Test Game Name',
            'short_name' => 'testgame',
            'buttons' => 'Test buttons',
            'webhook_url' => 'https://example.com/webhook',
        ];
    }
}
