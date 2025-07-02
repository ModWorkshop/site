<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Model;
use App\Models\User;
use Tests\AdminResourceTest;

class CategoryTest extends AdminResourceTest
{
    protected string $parentUrl = 'games';
    protected string $url = 'categories';
    protected bool $isGlobal = false;
    protected bool $hasParent = true;

    public function createDummy(?User $user = null, ?Model $parent = null): ?Category
    {        
        return Category::create([
            'name' => 'Test Category',
            'desc' => 'Test category description',
            'display_order' => 1,
            'game_id' => $parent->id,
            'thumbnail' => 'default.png',
            'webhook_url' => '',
        ]);
    }

    public function upsertData(?Model $parent, string $method): array
    {
        return [
            'name' => 'Test Category Name',
            'desc' => 'Test category description for API testing',
            'display_order' => 1,
            'buttons' => 'Test buttons',
            'webhook_url' => 'https://example.com/webhook',
        ];
    }
}
