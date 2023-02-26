<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    /**
     * Makes a simple thread.
     */
    public function makeASimpleGameThread($user=null)
    {
        if (isset($user)) {
            $this->actingAs($user);
        }

        return $this->post("forums/{$this->game->forum_id}/threads", [
            'name' => 'This is a test!',
            'content' => 'This is a test!',
        ]);
    }

    public function makeASimpleThread($user=null)
    {
        if (isset($user)) {
            $this->actingAs($user);
        }

        return $this->post("forums/1/threads", [
            'name' => 'This is a test!',
            'content' => 'This is a test!',
        ]);
    }

    public function test_new_user_should_not_post()
    {
        return $this->makeASimpleThread($this->unverifiedUser())->assertStatus(403)
            && $this->makeASimpleGameThread($this->unverifiedUser())->assertStatus(403);
    }

    public function test_guests_should_not_post()
    {
        return $this->makeASimpleGameThread()->assertStatus(403) 
            && $this->makeASimpleThread()->assertStatus(403);
    }

    public function test_verified_user_should_post()
    {
        return $this->makeASimpleGameThread($this->user())->assertStatus(201) 
            && $this->makeASimpleThread($this->user())->assertStatus(201);
    }

    public function test_banned_users_should_not_post()
    {
        return $this->makeASimpleGameThread($this->bannedUser())->assertStatus(403) 
            && $this->makeASimpleThread($this->bannedUser())->assertStatus(403);
    }

    public function test_game_banned_users_should_not_post()
    {
        return $this->makeASimpleGameThread($this->gameBannedUser())->assertStatus(403);
    }
}
