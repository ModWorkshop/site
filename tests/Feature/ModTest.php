<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ModTest extends TestCase
{
    protected function setUp(): void
    {
        $this->neededPermissions = ['manage-mods'];
        parent::setUp();
    }

    protected function createResourceData() {
        return [
            'name' => 'This is a test!',
            'desc' => 'This is a test!',
        ];
    }

    protected function editResourceData() {
        return [
            'name' => 'Edited name',
            'desc' => 'Edited description',
            'license' => 'Edited license',
            'changelog' => 'Edited changelog',
            'instructions' => 'Edited instructions',
            'short_desc' => 'Edited short description',
            'donation' => 'edited@donation.com',
            'version' => 'Edited version'
        ];
    }

    protected function createResourceUrl() {
        return "games/{$this->game->id}/mods";
    }

    protected function resourceUrl(int $id) {
        return "mods/{$id}";
    }

    protected function simpleResource(User $user = null) {
        return $this->mod($user);
    }

    // View mod
    #[DataProvider('modVisibilitiesOwn')]
    public function test_view_own_mod(string $visibility, int $status) {
        return $this->viewResource(null, $this->mod(null, ['visibility' => $visibility]), $status);
    }

    #[DataProvider('modVisibilitiesOwn')]
    public function test_view_member_mod(string $visibility, int $status) {
        return $this->viewResource(null, $this->memberedMod('collaborator', ['visibility' => $visibility]), $status);
    }

    #[DataProvider('modVisibilitiesAnothers')]
    public function test_view_anothers_public_mod(string $visibility, int $status) {
        return $this->viewResource(null, $this->anothersMod(['visibility' => $visibility]), $status);
    }

    public static function modVisibilitiesOwn() {
        return [
            'public' => ['public', 200],
            'private' => ['private', 200],
            'unlisted' => ['unlisted', 200]
        ];
    }

    public static function modVisibilitiesAnothers() {
        return [
            'public' => ['public', 200],
            'private' => ['private', 403],
            'unlisted' => ['unlisted', 200]
        ];
    }

    // Create mod
    public function test_create_mod_user() {
        return $this->createResource();
    }
    
    public function test_create_mod_guest() {
        return $this->asGuest()->createResource(status: 403);
    }

    public function test_create_mod_unverified_user() {
        return $this->createResource($this->unverifiedUser(), 403);
    }

    public function test_create_mod_bannned_user() {
        return $this->createResource($this->bannedUser(), 403);
    }

    public function test_create_mod_game_bannned_user() {
        return $this->createResource($this->gameBannedUser(), 403);
    }

    // Edit mod
    public function test_edit_mod_user() {
        $this->editResource();
    }

    public function test_edit_mod_banned_user() {
        $this->editResource($this->bannedUser(), null, 403);
    }

    public function test_edit_mod_game_banned_user() {
        $this->editResource($this->gameBannedUser(), null, 403);
    }

    public function test_edit_mod_unverified_user() {
        $this->editResource($this->unverifiedUser(), null, 403);
    }

    /**
     * Edit mod as non owner
     */
    public function test_edit_mod_non_member_user() {
        $this->editResource(null, $this->anothersResource(), 403);
    }

    public function test_edit_mod_guest() {
        return $this->asGuest()->editResource(null, $this->anothersResource(), 403);
    }

    public function test_edit_mod_moderator() {
        $this->editResource($this->userWithRole('manage-mods'), $this->anothersResource());
    }

    public function test_edit_mod_game_moderator() {
        $this->editResource($this->userWithGameRole('manage-mods'), $this->anothersResource());
    }

    #[DataProvider('editModMembersDataProvider')]
    public function test_edit_mod_members(string $level, int $status) {
        $this->editResource(null, $this->memberedMod($level), $status);
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
        $this->deleteResource();
    }

    public function test_delete_mod_banned_user() {
        $this->deleteResource($this->bannedUser());
    }

    public function test_delete_mod_game_banned_user() {
        $this->deleteResource($this->gameBannedUser());
    }

    public function test_delete_mod_unverified_user() {
        $this->deleteResource($this->unverifiedUser());
    }

    public function test_delete_guest() {
        $this->asGuest()->deleteResource(null, $this->anothersResource(), 403);
    }
    
    public function test_delete_mod_non_member_user() {
        $this->deleteResource(null, $this->anothersResource(), 403);
    }

    public function test_delete_mod_moderator() {
        $this->deleteResource($this->userWithRole('manage-mods'), $this->anothersResource());
    }

    public function test_delete_mod_game_moderator() {
        $this->deleteResource($this->userWithGameRole('manage-mods'), $this->anothersResource());
    }

    #[DataProvider('deleteModMembersDataProvider')]
    public function test_delete_mod_members(string $level, int $status) {
        $this->deleteResource(null, $this->memberedMod($level), $status);
    }

    public static function deleteModMembersDataProvider() {
        return [
            'maintainer' => ['maintainer', 200],
            'collaborator' => ['collaborator', 403],
            'contributor' => ['contributor', 403],
            'viewer' => ['viewer', 403],
        ];
    }
}
