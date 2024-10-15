<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use App\Models\Ban;
use App\Models\Forum;
use App\Models\Game;
use App\Models\Mod;
use App\Models\Role;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected Game $game;

    protected ?User $currentUser;

    protected function setUp(): void
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

        $this->actingAs($this->user());
    }
    
    /**
     * Makes a simple mod.
     */
    public function mod(User $user = null): ?Mod
    {
        return Mod::create([
            'name' => 'This is a test!',
            'desc' => 'This is a test!',
            'user_id' => $user?->id ?? $this->currentUser->id,
            'game_id' => $this->game->id
        ]);
    }

    public function unownedMod() {
        return $this->mod($this->user());
    }
    
    public function memberedMod(string $level, User $user = null) {
        $user ??= $this->currentUser;
        $mod = $this->unownedMod();
        $mod->members()->attach($user, [ 'level' => $level, 'accepted' => true ]);

        return $mod;
    }

    /**
     * Returns a regular user.
     */
    public function user(): User
    {
        return User::factory()->create();
    }

    public function admin(): User {
        $user = $this->user();
        $user->roles()->attach(Role::where('name', 'Admin')->first());

        return $user;
    }

    /**
     * Returns a user that hasn't verified their email yet.
     */
    public function unverifiedUser(): User
    {
        return User::factory()->create(['activated' => false]);
    }

    public function actingAs(Authenticatable $user = null, $guard = null)
    {
        $this->currentUser = $user;

        if ($user == null) {
            Auth::logout();
            return $this;
        }

        return $this->be($user, $guard);
    }

    /**
     * Sets the current user to be a regular verified user.
     */
    public function asUser() {
        return $this->actingAs($this->user());
    }

    /**
     * Sets the current user to be a guest.
     */
    public function asGuest() {
        return $this->actingAs();
    }

    /**
     * Sets the current user to be an unverified user.
     */
    public function asUnverifiedUser() {
        return $this->actingAs($this->unverifiedUser());
    }

    /**
     * Sets the current user to be a banned user.
     */
    public function asBannedUser() {
        return $this->actingAs($this->bannedUser());
    }

    /**
     * Sets the current user to be an admin.
     */
    public function asAdmin() {
        return $this->actingAs($this->admin());
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
