<?php

namespace Tests\Feature;

use App\Models\Mod;
use App\Models\Model;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\UserResourceTest;

class ModTest extends UserResourceTest
{
    protected string $parentUrl = 'games';
    protected string $url = 'mods';
    protected bool $isGlobal = false;
    protected bool $hasParent = true;

    public function makeParent(): void
    {
        $this->parent = $this->game;
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?Mod
    {        
        return Mod::create([
            'name' => 'Test Mod',
            'desc' => 'This is a test mod for testing purposes!',
            'user_id' => $user->id,
            'game_id' => $parent?->id ?? $this->game->id,
            'visibility' => 'public',
        ]);
    }

    public function upsertData(?Model $parent, string $method): array
    {
        return [
            'name' => 'Test Mod Name',   
            'desc' => 'Test mod description for API testing',
        ];
    }

    /**
     * Override create scenarios for mod-specific expectations
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403],
            'unverified' => ['unverified', 'unverified', 403], // Unverified can't create mods
            'verified' => ['verified', 'verified', 201],
            'banned' => ['banned', 'banned', 403],
            'game_banned' => ['game_banned', 'game_banned', 403],
            'admin' => ['admin', 'admin', 201],
        ];
    }

    // Mod-specific visibility tests
    #[DataProvider('modVisibilitiesOwn')]
    public function test_view_own_mod_visibility(string $visibility, int $status): void
    {
        $user = $this->user();
        $mod = $this->createDummy($user);
        $mod->update(['visibility' => $visibility]);
        
        $this->actingAs($user);
        $response = $this->getJson("mods/{$mod->id}");
        $response->assertStatus($status);
    }

    #[DataProvider('modVisibilitiesAnothers')]
    public function test_view_others_mod_visibility(string $visibility, int $status): void
    {
        $owner = $this->user();
        $viewer = $this->user();
        $mod = $this->createDummy($owner);
        $mod->update(['visibility' => $visibility]);
        
        $this->actingAs($viewer);
        $response = $this->getJson("mods/{$mod->id}");
        $response->assertStatus($status);
    }

    public static function modVisibilitiesOwn(): array
    {
        return [
            'public' => ['public', 200],
            'private' => ['private', 200],
            'unlisted' => ['unlisted', 200]
        ];
    }

    public static function modVisibilitiesAnothers(): array
    {
        return [
            'public' => ['public', 200],
            'private' => ['private', 403],
            'unlisted' => ['unlisted', 200]
        ];
    }
}
