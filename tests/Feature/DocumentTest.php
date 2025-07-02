<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Model;
use App\Models\User;
use Tests\AdminResourceTest;

class DocumentTest extends AdminResourceTest
{
    protected string $parentUrl = 'games';
    protected string $url = 'documents';
    protected bool $isGlobal = false;
    protected bool $hasParent = true;

    public function createDummy(?User $user = null, ?Model $parent = null): ?Document
    {
        return Document::create([
            'name' => 'Test Document',
            'url_name' => 'test-document',
            'desc' => 'Test document content for API testing',
            'game_id' => $parent->id
        ]);
    }

    public function upsertData(?Model $parent): array
    {
        return [
            'name' => 'Updated Test Document',
            'url_name' => 'updated-test-document',
            'desc' => 'Updated test document content for API testing',
        ];
    }

    /**
     * Documents are typically public for users to read game rules, guides, etc.
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 200], // Documents are usually public
            'unverified' => ['unverified', 'unverified', 200],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200],
            'game_banned' => ['game_banned', 'game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }

    /**
     * Only admins can create documents (rules, guides, etc.)
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403, 403],
            'unverified' => ['unverified', 'unverified', 403, 403],
            'verified' => ['verified', 'verified', 403, 403], // Regular users can't create documents
            'banned' => ['banned', 'banned', 403, 403],
            'game_banned' => ['game_banned', 'game_banned', 403, 403],
            'admin' => ['admin', 'admin', 201, 201], // Only admins can create documents
        ];
    }
}
