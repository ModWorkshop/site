<?php

namespace Tests\Feature;

use App\Models\FollowedUser;
use App\Models\Model;
use App\Models\User;
use Tests\FollowBlockResourceTest;

class FollowedUserTest extends FollowBlockResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'followed-users';
    protected bool $isGlobal = true;
    protected bool $hasParent = false;
    protected string $idKey = 'follow_user_id';

    public function makeParent(): void
    {
        // No parent needed for followed users
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?FollowedUser
    {
        return FollowedUser::create([
            'user_id' => $user->id,
            'follow_user_id' => $this->user()->id,
            'notify' => false,
        ]);
    }

    public function upsertData(?Model $parent, string $method): array
    {
        $followedUser = $this->user(); // Create another user to follow
        
        return [
            'follow_user_id' => $followedUser->id,
        ];
    }
}
