<?php

namespace Tests\Feature;

use App\Models\Forum;
use App\Models\Model;
use App\Models\Thread;
use App\Models\User;
use Tests\TestResource;
use Tests\UserResourceTest;

class ThreadTest extends UserResourceTest
{
    protected string $parentUrl = 'forums';
    protected string $url = 'threads';
    protected ?string $globalUrl = 'forums/1/threads';
    protected bool $isGlobal = true;
    protected bool $hasParent = true;

    protected int $forumCategoryId;
    protected int $globalForumCategoryId;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create category for global forum (ID 1)
        $globalForum = Forum::find(1);
        $this->globalForumCategoryId = $globalForum->categories()->first()->id ?? $globalForum->categories()->create(['name' => 'Test Category'])->id;
        
        // Ensure the game has a forum and load it with categories
        $this->game->ensureForumExists();
        $this->game->refresh();
        $this->game->load('forum.categories');
        
        // Get the first forum category created by default, or create one if none exists
        $this->forumCategoryId = $this->parent->categories()->first()->id ?? $this->parent->categories()->create(['name' => 'Test Category'])->id;
    }

    public function makeParent(): void
    {
        $this->game->ensureForumExists();
        $this->parent = $this->game->forum;
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?Thread
    {        
        // Determine which category to use based on the forum
        $categoryId = isset($parent) ? $this->forumCategoryId : $this->globalForumCategoryId;
        
        return Thread::create([
            'name' => 'Test Thread',
            'content' => 'This is a test thread for testing purposes!',
            'user_id' => $user->id,
            'forum_id' => $parent?->id ?? 1,
            'category_id' => $categoryId,
            'last_user_id' => $user->id,
            'bumped_at' => now(),
            'parser_version' => 2,
        ]);
    }

    public function upsertData(?Model $parent): array
    {
        // Use the appropriate category based on the forum
        $categoryId = isset($parent) ? $this->forumCategoryId : $this->globalForumCategoryId;
        
        return [
            'name' => 'Test Thread Name',
            'content' => 'Test thread content for API testing',
            'category_id' => $categoryId,
        ];
    }

    public static function createScenariosProvider(): array
    {
        $data = parent::createScenariosProvider();
        return [
            ...$data,
            'game_banned' => ['game_banned', 'game_banned', 201, 403], // Can create on global forum
        ];
    }

    public static function updateScenariosProvider(): array
    {
        $data = parent::updateScenariosProvider();
        return [
            ...$data,
            'game_banned' => ['game_banned', 'game_banned', 200, 403], // Can create on global forum
        ];
    }
}
