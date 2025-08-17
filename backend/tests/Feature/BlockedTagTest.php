<?php

namespace Tests\Feature;

use App\Models\BlockedTag;
use App\Models\Model;
use App\Models\Tag;
use App\Models\User;
use Tests\FollowBlockResourceTest;

class BlockedTagTest extends FollowBlockResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'blocked-tags';
    protected bool $isGlobal = true;
    protected bool $hasParent = false;
    protected string $idKey = 'tag_id';

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

    public function upsertData(?Model $parent, string $method): array
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
}
