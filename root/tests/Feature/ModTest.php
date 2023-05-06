<?php

namespace Tests\Feature;

use App\Models\Mod;
use App\Models\User;
use Tests\TestCase;
use Tests\TestResource;

class ModTest extends TestResource
{
    protected string $url = 'mods';
    protected bool $isGame = true;

    public function createDummy(User $user, int $parentId): ?Mod
    {
        return $this->mod($user);
    }

    public function upsertData()
    {
        return [
            'name' => 'This is a test!',
            'desc' => 'This is a test!',
        ];
    }
}
