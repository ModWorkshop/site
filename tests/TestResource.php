<?php

namespace Tests;

use App\Models\Ban;
use App\Models\Forum;
use App\Models\Game;
use App\Models\Model;
use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse as LaravelTestResponse;
use PHPUnit\Framework\Attributes\DataProvider;

abstract class TestResource extends TestCase
{
    protected ?Model $parent = null;
    protected string $parentUrl = 'games';
    protected string $url = '';
    protected ?string $globalUrl = null;
    protected bool $isGlobal = false;
    protected bool $hasParent = false;
    protected bool $isShallow = true;

    public function setUp(): void
    {
        parent::setUp();
        $this->makeParent();
    }

    /**
     * Get user based on scenario type
     */
    protected function getUserForScenario(string $userType): ?User
    {
        return match($userType) {
            'unverified' => $this->unverifiedUser(),
            'verified' => $this->user(),
            'banned' => $this->bannedUser(),
            'game_banned' => $this->gameBannedUser(),
            'other_game_banned' => $this->gameBannedUser($this->game2->id),
            'admin' => $this->admin(),
            default => null,
        };
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?Model
    {
        // This should be implemented by child classes
        return null;
    }

    public function upsertData(?Model $parent): array
    {
        return [];
    }

    public function makeParent(): void
    {
        /** @var Model $game */
        $game = $this->game;
        $this->parent = $game;
    }

    /**
     * In case you need to override the parent
     * For example, files have mods as parents, which are owned by the test user.
     */
    public function getParent(?User $user) {
        return $this->parent;
    }

    public function tryRequest(string $httpMethod, $data) {
        $url = '';

        $user = $data['user'] ?? null;
        $assertStatus = $data['assertStatus'] ?? 200;
        $parent = $data['parent'] ?? null;
        $owner = $data['owner'] ?? $user;

        $this->actingAs($user);

        $data = $httpMethod != 'GET' ? $this->upsertData($parent) : [];

        if ($httpMethod == 'POST') {
            $url = $this->getUrl($httpMethod, $parent, null, $data);
        } else {
            $resource = $this->createDummy($owner, $parent);
            if (!$resource) {
                $this->fail("Failed to create dummy resource for update test");
            }
            $url = $this->getUrl($httpMethod, $parent, $resource, $data);
        }

        echo $httpMethod . ' ' . $url . "\n";
        $req = $this->json($httpMethod, $url, $data);

        $this->debugResult($data['operation'] ?? 'Some Operation', $req, $assertStatus, $data['userType'] ?? 'user');

        return $req->assertStatus($assertStatus);
    }

    public function getUrl($httpMethod, ?Model $parent = null, ?Model $object = null, $data=null): string{
        if ($httpMethod == 'POST') {
            return isset($parent) ? "{$this->parentUrl}/{$parent->id}/{$this->url}" : $this->globalUrl ?? $this->url;
        } else {
            if ($this->isShallow) {
                return "{$this->url}/{$object->id}";
            } else {
                return isset($parent) ? "{$this->parentUrl}/{$parent->id}/{$this->url}/{$object->id}" : "{$this->url}/{$object->id}";
            }
        }
    }

    /**
     * Generic helper method to test scenarios for both global and game contexts
     */
    protected function testScenario(string $operation, $data) {
        $data['operation'] = $operation;

        return match($operation) {
            'create' => $this->tryRequest('POST', $data),
            'update' => $this->tryRequest('PATCH', $data),
            'delete' => $this->tryRequest('DELETE', $data),
            'view' => $this->tryRequest('GET', $data),  
            default => throw new \InvalidArgumentException("Unsupported operation: {$operation}")
        };
    }

    protected function testScenarios(string $operation, ?string $userType, int $expectedStatus, ?int $expectedParentedStatus = null): void {
        $user = $userType ? $this->getUserForScenario($userType) : null;

        // Test global context if applicable
        if ($this->isGlobal) {
            $this->testScenario($operation, [ 
                'assertStatus' => $expectedStatus,
                'user' => $user,
                'userType' => $userType,
            ]);
        }

        // Test game context if applicable
        if ($this->hasParent) {
            $this->testScenario($operation, [ 
                'assertStatus' => $expectedParentedStatus ?? $expectedStatus,
                'user' => $user,
                'userType' => $userType,
                'parent' => $this->getParent($user),
            ]);
        }
    }

    public function debugResult($operation, LaravelTestResponse $res, $expectedStatus, $userType): void{
        $actualStatus = $res->getStatusCode();
        if ($expectedStatus !== $actualStatus) {
            echo "\n=== GAME TEST FAILURE DEBUG INFO ===\n";
            echo "Operation: {$operation}\n";
            // echo "URL: {$re}\n";
            echo "User: {$userType}\n";
            // echo "Parent ID: {$parentId}\n";
            echo "Expected Status: {$expectedStatus}\n";
            echo "Actual Status: {$actualStatus}\n";
            // echo "Request Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
            echo "Response Content: {$res->getContent()}\n";
            echo "==============================\n";
        }
    }

    /**
     * Data provider for create scenarios
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403],
            'unverified' => ['unverified', 'unverified', 403],
            'verified' => ['verified', 'verified', 201],
            'banned' => ['banned', 'banned', 403],
            'game_banned' => ['game_banned', 'game_banned', 403],
            'other_game_banned' => ['other_game_banned', 'other_game_banned', 201],
            'admin' => ['admin', 'admin', 201],
        ];
    }

    /**
     * Data provider for update scenarios
     */
    public static function updateScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 403],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 403],
            'game_banned' => ['game_banned', 'game_banned', 403],
            'other_game_banned' => ['other_game_banned', 'other_game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }

    /**
     * Data provider for delete scenarios
     */
    public static function deleteScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 200],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200],
            'game_banned' => ['game_banned', 'game_banned', 200],
            'other_game_banned' => ['other_game_banned', 'other_game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }

    /**
     * Data provider for view scenarios
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'unverified' => ['unverified', 'unverified', 200],
            'verified' => ['verified', 'verified', 200],
            'banned' => ['banned', 'banned', 200],
            'game_banned' => ['game_banned', 'game_banned', 200],
            'other_game_banned' => ['other_game_banned', 'other_game_banned', 200],
            'admin' => ['admin', 'admin', 200],
        ];
    }

    /**
     * Test create scenarios using data provider
     */
    #[DataProvider('createScenariosProvider')]
    public function test_create_scenarios(string $scenarioName, ?string $userType, int $expectedStatus, ?int $expectedGameStatus=null): void
    {
        $this->testScenarios('create', $userType, $expectedStatus, $expectedGameStatus);
    }

    /**
     * Test update scenarios using data provider
     */
    #[DataProvider('updateScenariosProvider')]
    public function test_update_scenarios(string $scenarioName, ?string $userType, int $expectedStatus, ?int $expectedGameStatus=null): void
    {
        $this->testScenarios('update', $userType, $expectedStatus, $expectedGameStatus);
    }

    /**
     * Test delete scenarios using data provider
     */
    #[DataProvider('deleteScenariosProvider')]
    public function test_delete_scenarios(string $scenarioName, ?string $userType, int $expectedStatus, ?int $expectedGameStatus=null): void
    {
        $this->testScenarios('delete', $userType, $expectedStatus, $expectedGameStatus);
    }

    /**
     * Test view scenarios using data provider
     */
    #[DataProvider('viewScenariosProvider')]
    public function test_view_scenarios(string $scenarioName, ?string $userType, int $expectedStatus, ?int $expectedGameStatus=null): void
    {
        $this->testScenarios('view', $userType, $expectedStatus, $expectedGameStatus);
    }
}
