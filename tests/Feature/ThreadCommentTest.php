<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\TestResource;

class ThreadCommentTest extends TestResource
{
    protected string $parentUrl = 'threads';
    protected string $url = 'comments';
    protected bool $isGlobal = false;
    protected bool $isGame = true;

    public function makeParent()
    {
        $user = $this->user();
        $this->parent = Thread::create([
            'forum_id' => $this->game->forum_id,
            'user_id' => $user->id,
            'last_user_id' => $user->id,
            'name' => 'This is a test!',
            'content' => 'This is a test!',
        ]);
    }

    public function createDummy(User $user, int $parentId): ?Comment
    {
        return Comment::forceCreate([
            'content' => 'hello this is a test',
            'commentable_id' => $parentId,
            'commentable_type' => 'thread',
            'user_id' => $user->id
        ]);
    }

    public function upsertData()
    {
        return [
            'content' => 'This is a test!',
        ];
    }
}
