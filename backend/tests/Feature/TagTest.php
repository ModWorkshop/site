<?php

namespace Tests\Feature;

use App\Models\Model;
use App\Models\Tag;
use App\Models\User;
use Tests\AdminResourceTest;

class TagTest extends AdminResourceTest
{
    protected string $parentUrl = 'games';
    protected string $url = 'tags';
    protected bool $isGlobal = false;
    protected bool $hasParent = true;

    public function createDummy(?User $user = null, ?Model $parent = null): ?Tag
    {

        return Tag::create([
            'name' => 'Test Tag',
            'color' => '#FF0000',
            'game_id' => $parent->id,
            'notice' => 'Test tag notice',
            'notice_type' => 'warning',
            'type' => 'mod',
        ]);
    }

    public function upsertData(?Model $parent, string $method): array
    {
        return [
            'name' => 'test-tag',
            'color' => '#00FF00',
            'notice' => 'Test tag notice for API testing',
            'notice_type' => 'info',
            'type' => 'mod',
        ];
    }

    /**
     * Tags are typically public for displaying on mods/threads
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 200], // Tags are public for display purposes
            'unverified' => ['unverified', 'unverified', 200],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200],
            'game_banned' => ['game_banned', 'game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }
}
