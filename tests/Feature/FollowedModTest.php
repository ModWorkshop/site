<?php

namespace Tests\Feature;

use App\Models\FollowedMod;
use App\Models\User;
use App\Models\Mod;
use App\Models\Model;
use Tests\FollowBlockResourceTest;
use Tests\UserResourceTest;

class FollowedModTest extends FollowBlockResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'followed-mods';
    protected bool $isGlobal = true;
    protected bool $hasParent = false;
    protected string $idKey = 'mod_id';

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

    public function upsertData(?Model $parent, string $method): array
    {
        // Create a mod to follow - ownership doesn't matter
        $mod = Mod::factory()->create([
            'game_id' => $this->game->id,
        ]);

        return [
            'mod_id' => $mod->id,
        ];
    }
}
