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
use Tests\UserResourceTest;

class ModCommentTest extends UserResourceTest
{
    protected string $parentUrl = 'mods';
    protected string $url = 'comments';
    protected bool $isGlobal = false;
    protected bool $hasParent = true;

    public function makeParent(): void
    {
        /** @var Model $mod */
        $mod = $this->mod($this->user());
        $this->parent = $mod;
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?Comment
    {
        return Comment::forceCreate([
            'content' => 'hello this is a test',
            'commentable_id' => $parent->id,
            'commentable_type' => 'mod',
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
