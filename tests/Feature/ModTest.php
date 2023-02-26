<?php

namespace Tests\Feature;

use Tests\TestCase;

class ModTest extends TestCase
{
    /**
     * Verified users should be able to upload and the rest (guests, banned and unverified) shouldn't.
     */
    public function test_new_user_should_not_upload()
    {
        return $this->makeASimpleMod($this->unverifiedUser())->assertStatus(403);
    }

    public function test_guests_should_not_upload()
    {
        return $this->makeASimpleMod()->assertStatus(403);
    }

    public function test_verified_user_should_upload()
    {
        return $this->makeASimpleMod($this->user())->assertStatus(201);
    }

    public function test_banned_users_should_not_upload()
    {
        return $this->makeASimpleMod($this->bannedUser())->assertStatus(403);
    }

    public function test_game_banned_users_should_not_upload()
    {
        return $this->makeASimpleMod($this->gameBannedUser())->assertStatus(403);
    }
}
