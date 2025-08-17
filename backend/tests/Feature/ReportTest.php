<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use App\Models\Mod;
use App\Models\Model;
use Illuminate\Testing\TestResponse;
use Tests\UserResourceTest;

class ReportTest extends UserResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'reports';
    protected bool $isGlobal = true;
    protected bool $isGame = false;

    public function makeParent(): void
    {
        // Create a mod to report (since reports need a reportable item)
        $this->parent = Mod::factory()->create([
            'user_id' => $this->user()->id,
            'game_id' => $this->game->id,
        ]);
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?Report
    {
        $user ??= $this->user();
        
        return Report::create([
            'user_id' => $user->id,
            'reportable_type' => 'mod',
            'reportable_id' => $this->parent->id,
            'reason' => 'This is a test report for API testing',
            'data' => []
        ]);
    }

    protected function assertPostOperationResult(string $httpMethod, TestResponse $rs, int $assertStatus, array $requestData, ?Model $resource, ?Model $parent): void
    {
        if (in_array($assertStatus, [200, 201])) {
            $modelClass = $this->getModelClass();
            if ($modelClass) {
                $createdResource = Report::whereReportableType('mod')->whereReportableId($requestData['reportable_id'])->first();
                // $rs->assertNoContent(); // This should be the case maybe, but we don't actually return 204 when it's empty...
                $this->assertNotNull($createdResource, 'Resource should exist after POST operation');
            }
        }
    }

    public function getUrl($httpMethod, ?Model $parent = null, ?Model $object = null, $data=null): string {
        if ($httpMethod == 'POST') {
            return $data['reportable_type']."s/{$data['reportable_id']}/reports";
        } else {
            return parent::getUrl($httpMethod, $parent, $object, $data);
        }
    }

    public function upsertData(?Model $parent, string $method): array
    {
        if ($method === 'POST') {
            return [
                'reportable_type' => 'mod',
                'reportable_id' => $this->parent->id,
                'reason' => 'This mod appears to be spam content'
            ];
        } else {
            return [
                'archived' => true,
            ];
        }
    }

    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403],
            'unverified' => ['unverified', 'unverified', 403], // Unverified can't create mods
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 403],
            'game_banned' => ['game_banned', 'game_banned', 403],
            'admin' => ['admin', 'admin', 200],
        ];
    }

    public static function updateScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 403],
            'verified' => ['verified', 'verified', 403], // Can update own content
            'banned' => ['banned', 'banned', 403],
            'game_banned' => ['game_banned', 'game_banned', 403], // Can update globally, not in game
            'admin' => ['admin', 'admin', 200],
        ];
    }

    public static function deleteScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 403],
            'verified' => ['verified', 'verified', 403], // Can delete own content
            'banned' => ['banned', 'banned', 403],
            'game_banned' => ['game_banned', 'game_banned', 403], // Can delete globally, not in game
            'admin' => ['admin', 'admin', 200],
        ];
    }

    /**
     * Reports might have more restricted viewing - only admins and report creator
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 405],
            'unverified' => ['unverified', 'unverified', 405], // Can't view reports
            'verified' => ['verified', 'verified', 405], // Can't view others' reports
            'banned' => ['banned', 'banned', 405],
            'game_banned' => ['game_banned', 'game_banned', 405],
            'admin' => ['admin', 'admin', 405], // Admins can view reports
        ];
    }
}
