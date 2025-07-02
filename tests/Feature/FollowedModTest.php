<?php

namespace Tests\Feature;

use App\Models\FollowedMod;
use App\Models\User;
use App\Models\Mod;
use App\Models\Model;
use Tests\PersonalResourceTest;
use Tests\UserResourceTest;

class FollowedModTest extends PersonalResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'followed-mods';
    protected bool $isGlobal = true;
    protected bool $hasParent = false;

    public function makeParent(): void
    {

    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?FollowedMod
    {        
        // Create a mod to follow - ownership doesn't matter for following
        $mod = Mod::factory()->create([
            'game_id' => $this->game->id,
        ]);
        
        return FollowedMod::create([
            'user_id' => $user->id,
            'mod_id' => $mod->id,
            'notify' => false,
        ]);
    }

    public function upsertData(?Model $parent): array
    {
        // Create a mod to follow - ownership doesn't matter
        $mod = Mod::factory()->create([
            'game_id' => $this->game->id,
        ]);

        return [
            'mod_id' => $mod->id,
        ];
    }

    /**
     * Override scenarios - followed games are personal and private
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 405],
            'verified' => ['verified', 'verified', 405],
            'banned' => ['banned', 'banned', 405],
            'game_banned' => ['game_banned', 'game_banned', 405],
            'admin' => ['admin', 'admin', 405],
        ];
    }

    /**
     * Users can manage their own followed games
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 401],
            'unverified' => ['unverified', 'unverified', 200],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200],
            'game_banned' => ['game_banned', 'game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }

    /**
     * Data provider for update scenarios
     */
    public static function updateScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 405],
            'verified' => ['verified', 'verified', 405],
            'banned' => ['banned', 'banned', 405],
            'game_banned' => ['game_banned', 'game_banned', 405],
            'admin' => ['admin', 'admin', 405],
        ];
    }
}
