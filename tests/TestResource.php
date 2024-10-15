<?php

namespace Tests;

use App\Models\Ban;
use App\Models\Forum;
use App\Models\Game;
use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestResource extends TestCase
{
    protected Model $parent;

    protected string $parentUrl = 'games';
    protected string $url = '';
    protected bool $isGlobal = false;
    protected bool $isGame = false;

    public function setUp(): void
    {
        parent::setUp();
        $this->makeParent();
    }

    public function createDummy(User $user, int $parentId): ?Model
    {
        return Model::create();
    }

    public function upsertData()
    {
        return [];
    }

    public function makeParent()
    {
        $this->parent = $this->game;
    }

    /**
     * Makes a simple thread.
     */
    public function tryCreate($assertStatus=200, $user=null, int $parentId=null)
    {
        if (isset($user)) {
            $this->actingAs($user);
        }

        if ($this->isGlobal && !isset($parentId)) {
            $parentId = 1;
        }
 
        if (!isset($parentId)) {
            return true;
        }

        $req = $this->post("{$this->parentUrl}/{$parentId}/{$this->url}", $this->upsertData());

        $code = $req->getStatusCode();
        if ($code !== $assertStatus) {
            debug_print_backtrace();
            var_dump("CREATE EXPECTED {$assertStatus} GOT {$code} {$this->parentUrl}/{$parentId}/{$this->url} ");
        }

        return $req->assertStatus($assertStatus);
    }

    /**
     * Edit thread
     */
    public function tryUpdate($assertStatus=200, $user=null, $actingAs=null, int $parentId = null)
    {
        $actingAs ??= $user;
        if (isset($actingAs)) {
            $this->actingAs($actingAs);
        }

        if ($this->isGlobal && !isset($parentId)) {
            $parentId = 1;
        }

        if (!isset($parentId)) {
            return true;
        }

        $resource = $this->createDummy($user, $parentId);

        $req = $this->patch("{$this->url}/{$resource->id}", $this->upsertData());

        $code = $req->getStatusCode();
        if ($code !== $assertStatus) {
            debug_print_backtrace();
            var_dump("UPDATE EXPECTED {$assertStatus} GOT {$code} {$this->url}/{$resource->id}");
        }

        return $req->assertStatus($assertStatus);
    }

    public function tryDelete($assertStatus=200, $user=null, $actingAs=null, int $parentId = null)
    {
        $actingAs ??= $user;

        if (isset($actingAs)) {
            $this->actingAs($actingAs);
        }

        if ($this->isGlobal && !isset($parentId)) {
            $parentId = 1;
        }

        if (!isset($parentId)) {
            return true;
        }

        $resource = $this->createDummy($user, $parentId);

        $req = $this->delete("{$this->url}/{$resource->id}");


        $code = $req->getStatusCode();
        if ($code !== $assertStatus) {
            debug_print_backtrace();
            var_dump("DELETE EXPECTED {$assertStatus} GOT {$code} {$this->url}/{$resource->id}");
        }

        return $req->assertStatus($assertStatus);
    }

    public function tryParentCreate($assertStatus=200, $user=null)
    {
        if (!$this->isGame) {
            return true;
        }

        return $this->tryCreate($assertStatus, $user, $this->parent->id);
    }

    public function tryParentUpdate($assertStatus=200, $user=null, $actingAs=null)
    {
        if (!$this->isGame) {
            return true;
        }

        return $this->tryUpdate($assertStatus, $user, $actingAs, $this->parent->id);
    }

    public function tryParentDelete($assertStatus=200, $user=null, $actingAs=null)
    {
        if (!$this->isGame) {
            return true;
        }

        return $this->tryDelete($assertStatus, $user, $actingAs, $this->parent->id);
    }

    public function test_create()
    {
        $unverifiedUser = $this->unverifiedUser();
        $verifiedUser = $this->user();
        $bannedUser = $this->bannedUser();

        return $this->tryCreate(403, $unverifiedUser) && $this->tryParentCreate(403, $unverifiedUser)
            && $this->tryParentCreate(403) && $this->tryCreate(403)
            && $this->tryParentCreate(201, $verifiedUser) && $this->tryCreate(201, $verifiedUser)
            && $this->tryParentCreate(403, $bannedUser) && $this->tryCreate(403, $bannedUser)
            && $this->tryParentCreate(403, $this->gameBannedUser());
    }

    /**
     * Edit
     */
    public function test_edit()
    {
        $user = $this->user();
        $bannedUser = $this->bannedUser();
        $gameBannedUser = $this->gameBannedUser();
        $otherUser = $this->user();

        return $this->tryUpdate(403, $bannedUser)
            && $this->tryParentUpdate(403, $bannedUser)
            && $this->tryUpdate(200, $gameBannedUser)
            && $this->tryParentUpdate(403, $gameBannedUser)
            && $this->tryUpdate(200, $user)
            && $this->tryParentUpdate(200, $user)
            && $this->tryUpdate(403, $otherUser, $user)
            && $this->tryParentUpdate(403, $otherUser, $user);
    }


    /**
     * Delete
     */
    public function test_delete()
    {
        $user = $this->user();
        $bannedUser = $this->bannedUser();
        $gameBannedUser = $this->gameBannedUser();
        $otherUser = $this->user();

        return $this->tryDelete(200, $user)
            && $this->tryParentDelete(200, $user)
            && $this->tryDelete(200, $bannedUser)
            && $this->tryParentDelete(200, $bannedUser)
            && $this->tryDelete(200, $gameBannedUser)
            && $this->tryParentDelete(200, $gameBannedUser)
            && $this->tryDelete(403, $otherUser, $user)
            && $this->tryParentDelete(403, $otherUser, $user);
    }
}
