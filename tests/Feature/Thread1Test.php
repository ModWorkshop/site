<?php

namespace Tests\Feature;

// use App\Models\Thread;
// use App\Models\User;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
// use Tests\TestCase;
// use Tests\TestResource;

// class ThreadTest extends TestResource
// {
//     protected string $parentUrl = 'forums';
//     protected string $url = 'threads';
//     protected bool $isGlobal = true;
//     protected bool $isGame = true;

//     public function createDummy(User $user, int $parentId): ?Thread
//     {
//         return Thread::create([
//             'forum_id' => $parentId,
//             'user_id' => $user->id,
//             'last_user_id' => $user->id,
//             'name' => 'This is a test!',
//             'content' => 'This is a test!',
//         ]);
//     }

//     public function upsertData()
//     {
//         return [
//             'name' => 'This is a test!',
//             'content' => 'This is a test!',
//         ];
//     }

//     public function tryParentCreate($assetStatus=200, $user=null)
//     {
//         return $this->tryCreate($assetStatus, $user, $this->game->forum_id);
//     }

//     public function tryParentUpdate($assetStatus=200, $user=null, $actingAs=null)
//     {
//         return $this->tryUpdate($assetStatus, $user, $actingAs, $this->game->forum_id);
//     }

//     public function tryParentDelete($assetStatus=200, $user=null, $actingAs=null)
//     {
//         return $this->tryDelete($assetStatus, $user, $actingAs, $this->game->forum_id);
//     }
// }
