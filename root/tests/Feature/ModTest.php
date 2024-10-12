<?php

namespace Tests\Feature;

use App\Models\GameRole;
use App\Models\Mod;
use App\Models\Role;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ModTest extends TestCase
{
    protected string $url = 'mods';
    protected bool $isGame = true;

    private ?Mod $currentMod;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create([
            'name' => "Manage Mods",
            'order' => 1
        ])->permit('manage-mods');

        GameRole::create([
            'name' => "Manage Mods",
            'order' => 1,
            'game_id' => $this->game->id
        ])->permit('manage-mods');
    }

    function manageModsUser(): User {
        $user = $this->user();
        $user->roles()->attach(Role::where('name', 'Manage Mods')->first());

        return $user;
    }

    function gameManageModsUser(): User {
        $user = $this->user();
        $user->gameRoles()->attach($this->game->roles()->where('name', 'Manage Mods')->first());

        return $user;
    }

    function asManageModsUser() {
        $this->actingAs($this->manageModsUser());
        return $this;
    }

    function asGameManageModsUser() {
        $this->actingAs($this->gameManageModsUser());
        return $this;
    }

    public function usingMod(Mod $mod = null) {
        $this->currentMod = $mod ?? $this->mod($this->currentUser);
        return $this;
    }

    // Create mod
    public function test_create_mod_user() {
        return $this->createMod();
    }
    
    public function test_create_mod_guest() {
        return $this->asGuest()->createMod(status: 403);
    }

    public function test_create_mod_unverified_user() {
        return $this->createMod($this->unverifiedUser(), 403);
    }

    public function test_create_mod_bannned_user() {
        return $this->createMod($this->bannedUser(), 403);
    }

    // Edit mod
    public function test_edit_mod_user() {
        $this->editMod();
    }

    public function test_edit_mod_banned_user() {
        $this->editMod($this->bannedUser(), null, 403);
    }

    public function test_edit_mod_unverified_user() {
        $this->editMod($this->unverifiedUser(), null, 403);
    }

    /**
     * Edit mod as non owner
     */
    public function test_edit_mod_non_member_user() {
        $this->editMod(null, $this->unownedMod(), 403);
    }

    public function test_edit_mod_guest() {
        return $this->asGuest()->editMod(null, $this->unownedMod(), 403);
    }

    public function test_edit_mod_moderator() {
        $this->editMod($this->manageModsUser(), $this->unownedMod());
    }

    public function test_edit_mod_game_moderator() {
        $this->editMod($this->gameManageModsUser(), $this->unownedMod());
    }

    #[DataProvider('editModMembersDataProvider')]
    public function test_edit_mod_members(string $level, int $status) {
        $this->editMod(null, $this->memberedMod($level), $status);
    }

    public static function editModMembersDataProvider() {
        return [
            'maintainer' => ['maintainer', 200],
            'collaborator' => ['collaborator', 200],
            'contributor' => ['contributor', 403],
            'viewer' => ['viewer', 403],
        ];
    }

    // Delete mod
    public function test_delete_mod_user() {
        $this->deleteMod();
    }

    public function test_delete_mod_banned_user() {
        $this->deleteMod($this->bannedUser());
    }

    public function test_delete_mod_unverified_user() {
        $this->deleteMod($this->unverifiedUser());
    }

    public function test_delete_guest() {
        $this->asGuest()->deleteMod(null, $this->unownedMod(), 403);
    }
    
    public function test_delete_mod_non_member_user() {
        $this->deleteMod(null, $this->unownedMod(), 403);
    }

    public function test_delete_mod_moderator() {
        $this->deleteMod($this->manageModsUser(), $this->unownedMod());
    }

    #[DataProvider('deleteModMembersDataProvider')]
    public function test_delete_mod_members(string $level, int $status) {
        $this->deleteMod(null, $this->memberedMod($level), $status);
    }

    public static function deleteModMembersDataProvider() {
        return [
            'maintainer' => ['maintainer', 200],
            'collaborator' => ['collaborator', 403],
            'contributor' => ['contributor', 403],
            'viewer' => ['viewer', 403],
        ];
    }

    public function createMod(User $user = null, $status = 201) {
        if (isset($user)) {
            $this->actingAs($user);
        }

        $this->post("games/{$this->game->id}/mods", [
            'name' => 'This is a test!',
            'desc' => 'This is a test!',
        ])->assertStatus($status);
    }

    public function editMod(User $user = null, Mod $mod = null, int $status = 200) {
        if (isset($user)) {
            $this->actingAs($user);
        }
        $mod ??= $this->currentMod ?? $this->mod($user);

        $data = [
            'name' => 'Edited name',
            'desc' => 'Edited description',
            'license' => 'Edited license',
            'changelog' => 'Edited changelog',
            'instructions' => 'Edited instructions',
            'short_desc' => 'Edited short description',
            'donation' => 'edited@donation.com',
            'version' => 'Edited version'
        ];

        $this->patch("mods/{$mod->id}", $data)
            ->assertStatus($status);

        $freshMod = $mod->fresh();
        // If success, should change otherwise nothing should change
        if ($status == 200) {
            foreach($data as $k => $v) {
                $this->assertEquals($freshMod[$k], $v);
            }
        } else {
            foreach($data as $k) {
                $this->assertEquals($mod[$k], $freshMod[$k]);
            }
        }
    }

    public function deleteMod(User $user = null, Mod $mod = null, int $status = 200) {
        if (isset($user)) {
            $this->actingAs($user);
        }
        $mod ??= $this->currentMod ?? $this->mod($user);

        $this->delete("mods/{$mod->id}")
            ->assertStatus($status);

        $freshMod = $mod->fresh();
        // If success, should be deleted
        if ($status == 200) {
            $this->assertNull($freshMod);
        } else {
            $this->assertNotNull($freshMod);
        }
    }
}
