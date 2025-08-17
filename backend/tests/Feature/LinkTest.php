<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\Mod;
use App\Models\Model;
use App\Models\User;
use Tests\UserResourceTest;

class LinkTest extends UserResourceTest
{
    protected string $parentUrl = 'mods';
    protected string $url = 'links';
    protected bool $isGlobal = false;
    protected bool $hasParent = true;

    public function makeParent(): void
    {
        
    }

    public function getParent(?User $user) {
        return Mod::create([
            'name' => 'Test Mod Parent',
            'desc' => 'This is a test mod for file testing',
            'user_id' => $user->id ?? $this->user()->id, // Use owner if set, otherwise some user
            'game_id' => $this->game->id,
            'visibility' => 'public',
        ]);
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?Link
    {        
        return Link::create([
            'name' => 'Test Link',
            'url' => 'https://example.com/test-link',
            'desc' => 'This is a test link for API testing',
            'mod_id' => $parent->id, // Use the parent mod
            'user_id' => $user->id,
        ]);
    }

    public function upsertData(?Model $parent, string $method): array
    {
        return [
            'name' => 'Updated Test Link',
            'url' => 'https://example.com/updated-test-link',
            'desc' => 'Updated test link description'
        ];
    }

    /**
     * Links are typically public but may have restrictions
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 200, 200],
            'verified' => ['verified', 'verified', 200, 200],
            'banned' => ['banned', 'banned', 200, 200],
            'game_banned' => ['game_banned', 'game_banned', 200, 200],
            'admin' => ['admin', 'admin', 200, 200],
        ];
    }

    /**
     * Verified users can upload links, but need mod ownership or permissions
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403, 403],
            'unverified' => ['unverified', 'unverified', 403, 403], // Need verification to upload
            'verified' => ['verified', 'verified', 201, 201], // Can upload files
            'banned' => ['banned', 'banned', 403, 403], // Banned users can't upload
            'game_banned' => ['game_banned', 'game_banned', 201, 403], // Can upload globally, not in game
            'admin' => ['admin', 'admin', 201, 201],
        ];
    }

    public static function deleteScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 403],
            'verified' => ['verified', 'verified', 200], // Can delete own content
            'banned' => ['banned', 'banned', 403],
            'game_banned' => ['game_banned', 'game_banned', 200, 403], // Can delete globally, not in game
            'admin' => ['admin', 'admin', 200],
        ];
    }
}
