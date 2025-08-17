<?php

namespace Tests\Feature;

use App\Models\BlockedUser;
use App\Models\Model;
use App\Models\User;
use Tests\FollowBlockResourceTest;

class BlockedUserTest extends FollowBlockResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'blocked-users';
    protected bool $isGlobal = true;
    protected bool $hasParent = false;
    protected string $idKey = 'block_user_id'; // Use block_user_id as the ID key for blocked users

    public function makeParent(): void
    {
        // No parent needed for blocked users
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?BlockedUser
    {
        echo 'blocked user : ' . $user->id . PHP_EOL;
        return BlockedUser::create([
            'user_id' => $user->id,
            'block_user_id' => $this->user()->id,
            'silent' => false,
        ]);
    }

    public function upsertData(?Model $parent, string $method): array
    {
        $blockedUser = $this->user(); // Create another user to block
        
        return [
            'block_user_id' => $blockedUser->id,
            'silent' => true,
        ];
    }
}
