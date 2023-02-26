<?php

namespace Tests;

use App\Models\Ban;
use App\Models\Forum;
use App\Models\Game;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected Game $game;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['class' => 'RolePermissionsSeeder']);

        User::forceCreate([
            'id' => 1,
            'name' => 'ModWorkshop',
            'unique_name' => '',
            'email' => 'luffydafloffi@gmail.com',
            'email_verified_at' => Carbon::now(),
        ], 'id');

        $this->game = Game::forceCreate([
            'name' => 'Test: The Game',
            'short_name' => 'test'
        ], 'short_name');

        Forum::create(['name' => 'global']);

        DB::select("SELECT SETVAL(pg_get_serial_sequence('users', 'id'), (SELECT MAX(id) FROM users));");
        DB::select("SELECT SETVAL(pg_get_serial_sequence('games', 'id'), (SELECT MAX(id) FROM games));");
    }

    /**
     * Makes a simple mod.
     */
    public function makeASimpleMod($user=null)
    {
        if (isset($user)) {
            $this->actingAs($user);
        }

        return $this->post("games/test/mods", [
            'name' => 'This is a test!',
            'desc' => 'This is a test!',
        ]);
    }

    /**
     * Returns a regular user.
     */
    public function user(): User
    {
        return User::factory()->create();
    }

    /**
     * Returns a user that hasn't verified their email yet.
     */
    public function unverifiedUser(): User
    {
        return User::factory()->create(['email_verified_at' => null]);
    }

    /**
     * Returns a user that has been banned globally.
     */
    public function bannedUser(): User
    {
        $user = User::factory()->create();
        Ban::create([
            'user_id' => $user->id,
            'expire_date' => Carbon::now()->addDay(1),
            'reason' => 'test!'
        ]);
        $user->load('ban');

        return $user;
    }

    /**
     * Returns a game banned user.
     */
    public function gameBannedUser(): User
    {
        $user = User::factory()->create();
        Ban::create([
            'user_id' => $user->id,
            'expire_date' => Carbon::now()->addDay(1),
            'reason' => 'test!',
            'game_id' => $this->game->id
        ]);
        
        return $user;
    }

}
