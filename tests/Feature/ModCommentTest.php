<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Mod;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\TestResource;

class ModCommentTest extends TestResource
{
    protected string $parentUrl = 'mods';
    protected string $url = 'comments';
    protected bool $isGlobal = false;
    protected bool $isGame = true;

    public function makeParent()
    {
        $this->parent = $this->mod($this->user());
    }

    public function createDummy(User $user, int $parentId): ?Comment
    {
        return Comment::forceCreate([
            'content' => 'hello this is a test',
            'commentable_id' => $parentId,
            'commentable_type' => 'mod',
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
