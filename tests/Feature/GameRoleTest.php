<?php

namespace Tests\Feature;

use App\Models\GameRole;
use App\Models\Model;
use App\Models\User;
use Tests\AdminResourceTest;

class GameRoleTest extends AdminResourceTest
{
    protected string $parentUrl = 'games';
    protected string $url = 'roles';
    protected bool $isGlobal = false;
    protected bool $hasParent = true;
    protected bool $isShallow = false;

    public function createDummy(?User $user = null, ?Model $parent = null): ?GameRole
    {
        $user ??= $this->admin(); // Only admins can create game roles
        
        return GameRole::create([
            'name' => 'Test Game Role',
            'desc' => 'Test game role description',
            'color' => '#FF0000',
            'game_id' => $parent->id,
            'order' => 1000,
            'is_vanity' => false,
        ]);
    }

    public function upsertData(?Model $parent): array
    {
        return [
            'name' => 'test-game-role',
            'desc' => 'Test game role description for API testing',
            'color' => '#00FF00',
            'order' => 500,
            'is_vanity' => false,
        ];
    }

    /**
     * Game roles might be viewable by everyone but only manageable by admins
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 200], // Game roles might be public for displaying user roles
            'unverified' => ['unverified', 'unverified', 200],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200],
            'game_banned' => ['game_banned', 'game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }
}
