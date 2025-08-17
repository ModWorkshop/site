<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\Model;
use App\Models\User;
use Tests\AdminResourceTest;

class ForumCategoryTest extends AdminResourceTest
{
    protected string $parentUrl = 'games';
    protected string $url = 'forum-categories';
    protected bool $isGlobal = false;
    protected bool $hasParent = true;

    public function makeParent(): void
    {
        // Forum categories belong to games, not forums
        $this->parent = $this->game;
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?ForumCategory
    {
        $user ??= $this->admin(); // Only admins can create forum categories
        // Forum categories belong to forums, so we need to get the forum from the game
        $forum = $parent->forum;
        
        return ForumCategory::create([
            'name' => 'Test Forum Category',
            'desc' => 'Test forum category description',
            'forum_id' => $forum->id, // Use the forum's ID, not the game's ID
            'emoji' => '📋',
            'display_order' => 1,
            'is_private' => false,
            'private_threads' => false,
            'banned_can_post' => false,
            'hidden' => false,
            'can_close_threads' => false,
            'grid_mode' => false,
        ]);
    }

    public function upsertData(?Model $parent, string $method): array
    {
        return [
            'name' => 'Updated Test Forum Category',
            'desc' => 'Updated test forum category description',
            'emoji' => '📝',
            'display_order' => 2,
            'is_private' => false,
            'private_threads' => false,
            'banned_can_post' => false,
            'hidden' => false,
            'can_close_threads' => true,
            'grid_mode' => false,
        ];
    }

    /**
     * Forum categories are typically public for viewing
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 200], // Forum categories are usually public
            'unverified' => ['unverified', 'unverified', 200],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200],
            'game_banned' => ['game_banned', 'game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }

    /**
     * Only admins can create forum categories
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403, 403],
            'unverified' => ['unverified', 'unverified', 403, 403],
            'verified' => ['verified', 'verified', 403, 403], // Regular users can't create categories
            'banned' => ['banned', 'banned', 403, 403],
            'game_banned' => ['game_banned', 'game_banned', 403, 403],
            'admin' => ['admin', 'admin', 201, 201], // Only admins can create forum categories
        ];
    }
}
