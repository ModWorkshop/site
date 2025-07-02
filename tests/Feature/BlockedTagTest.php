<?php

namespace Tests\Feature;

use App\Models\BlockedTag;
use App\Models\Model;
use App\Models\Tag;
use App\Models\User;
use Tests\PersonalResourceTest;

class BlockedTagTest extends PersonalResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'blocked-tags';
    protected bool $isGlobal = true;
    protected bool $hasParent = false;

    public function makeParent(): void
    {
        // No parent needed for blocked tags
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?BlockedTag
    {
        // Create a tag to block
        $tag = Tag::create([
            'name' => 'Test Blocked Tag',
            'color' => '#FF0000',
            'game_id' => $this->game->id,
            'type' => 'mod',
        ]);
        
        return BlockedTag::create([
            'user_id' => $user->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function upsertData(?Model $parent): array
    {
        // Create a tag to block
        $tag = Tag::create([
            'name' => 'Another Test Tag',
            'color' => '#00FF00',
            'game_id' => $this->game->id,
            'type' => 'mod',
        ]);
        
        return [
            'tag_id' => $tag->id,
        ];
    }

    /**
     * Users can manage their own blocked tags
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 401], // Must be authenticated
            'unverified' => ['unverified', 'unverified', 200], // Can block tags
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200], // Even banned users can block tags
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
