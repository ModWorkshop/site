<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Tests\UserResourceTest;

class ThreadCommentTest extends UserResourceTest
{
    protected string $parentUrl = 'threads';
    protected string $url = 'comments';
    protected bool $isGlobal = false;
    protected bool $hasParent = true;

    public function makeParent(): void
    {
        // Use owner if set, otherwise fall back to current user
        $user = $this->owner ?? $this->user();
        /** @var Model $thread */
        $thread = Thread::create([
            'forum_id' => $this->game->forum_id ?? 1,
            'user_id' => $user->id,
            'last_user_id' => $user->id,
            'name' => 'This is a test!',
            'content' => 'This is a test!',
        ]);
        $this->parent = $thread;
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?Comment
    {        
        return Comment::forceCreate([
            'content' => 'hello this is a test',
            'commentable_id' => $parent->id,
            'commentable_type' => 'thread',
            'user_id' => $user->id
        ]);
    }

    public function upsertData(?Model $parent): array
    {
        return [
            'content' => 'This is a test!',
        ];
    }
}
