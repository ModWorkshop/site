<?php

namespace Tests\Feature;

use App\Models\Model;
use App\Models\Role;
use App\Models\User;
use Tests\AdminResourceTest;

class RoleTest extends AdminResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'roles';
    protected bool $isGlobal = true;
    protected bool $hasParent = false;

    public function makeParent(): void
    {

    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?Role
    {
        return Role::create([
            'name' => 'Test Role',
            'display_name' => 'Test Role Display',
            'order' => -1000,
            'description' => 'Test role for API testing',
            'color' => '#FF0000',
        ]);
    }

    public function upsertData(?Model $parent): array
    {
        return [
            'name' => 'test-role',
            'display_name' => 'Test Role Display Name',
            'description' => 'Test role description for API testing',
            'order' => -500,
            'color' => '#00FF00',
        ];
    }

    /**
     * Roles might be viewable by everyone but only manageable by admins
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 200], // Roles might be public for displaying user roles
            'unverified' => ['unverified', 'unverified', 200],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200],
            'game_banned' => ['game_banned', 'game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }
}
