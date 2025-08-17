<?php

namespace Tests;

use App\Models\Model;
use App\Models\User;
use Illuminate\Testing\TestResponse;
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
    protected bool $postReturnsNoContent = false;
    protected array $inconsistentData = []; // Fields that may have inconsistent data across requests, for example role orders
    protected string $idKey = 'id';

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

    public function upsertData(?Model $parent, string $method): array
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

        $requestData = $httpMethod != 'GET' ? $this->upsertData($parent, $httpMethod) : [];
        $resource = null;

        if ($httpMethod == 'POST') {
            $url = $this->getUrl($httpMethod, $parent, null, $requestData);
        } else {
            $resource = $this->createDummy($owner, $parent);
            if (!$resource) {
                $this->fail("Failed to create dummy resource for update test");
            }
            $url = $this->getUrl($httpMethod, $parent, $resource, $requestData);
        }

        echo $httpMethod . ' ' . $url . "\n";
        $req = $this->json($httpMethod, $url, $requestData);

        $this->debugResult($data['operation'] ?? 'Some Operation', $req, $assertStatus, $data['userType'] ?? 'user');

        $response = $req->assertStatus($assertStatus);

        // Add assertions based on operation and status
        $this->assertOperationResult($httpMethod, $response, $assertStatus, $requestData, $resource, $parent);

        return $response;
    }

    public function getUrl($httpMethod, ?Model $parent = null, ?Model $object = null, $data=null): string{
        if ($httpMethod == 'POST') {
            return isset($parent) ? "{$this->parentUrl}/{$parent->id}/{$this->url}" : $this->globalUrl ?? $this->url;
        } else {
            $idKey = $this->idKey;
            if ($this->isShallow) {
                return "{$this->url}/{$object->$idKey}";
            } else {
                return isset($parent) ? "{$this->parentUrl}/{$parent->id}/{$this->url}/{$object->$idKey}" : "{$this->url}/{$object->id}";
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

    public function debugResult($operation, TestResponse $res, $expectedStatus, $userType): void{
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
     * Assert that the operation result matches expectations
     */
    protected function assertOperationResult(string $httpMethod, TestResponse $rs, int $assertStatus, array $requestData, ?Model $resource, ?Model $parent): void
    {
        match($httpMethod) {
            'POST' => $this->assertPostOperationResult($httpMethod, $rs, $assertStatus, $requestData, $resource, $parent),
            'PATCH' => $this->assertPatchOperationResult($httpMethod, $rs, $assertStatus, $requestData, $resource, $parent),
            'DELETE' => $this->assertDeleteOperationResult($httpMethod, $rs, $assertStatus, $requestData, $resource, $parent),
            'GET' => $this->assertGetOperationResult($httpMethod, $rs, $assertStatus, $requestData, $resource, $parent),
            default => throw new \InvalidArgumentException("Unsupported HTTP method: {$httpMethod}"),
        };
    }

    protected function assertPostOperationResult(string $httpMethod, TestResponse $rs, int $assertStatus, array $requestData, ?Model $resource, ?Model $parent): void
    {
        if (in_array($assertStatus, [200, 201])) {
            $modelClass = $this->getModelClass();
            if ($modelClass) {
                // Try to find the most recently created resource that matches our data
                $createdResource = $modelClass::latest('id')->first();
                $json = $rs->json();
                $this->assertNotNull($createdResource, 'Created resource should exist in database');
                
                // Verify the created resource has the expected data
                foreach ($requestData as $key => $value) {
                    if (!($this->inconsistentData[$key] ?? false) && (is_string($value) || is_numeric($value) || is_bool($value))) {
                        if ($createdResource->hasAttribute($key)) {
                            $this->assertEquals($value, $json[$key], "Created resource field '{$key}' should match request data");
                        }
                    }
                }
            }
        }
    }

    protected function assertPatchOperationResult(string $httpMethod, TestResponse $rs, int $assertStatus, array $requestData, ?Model $resource, ?Model $parent): void
    {
        if ($assertStatus === 200 && $resource) {
            // Refresh the resource from database and verify changes
            $resource->refresh();
            foreach ($requestData as $key => $value) {
                // Skip file uploads and complex data types
                if (!($this->inconsistentData[$key] ?? false) && (is_string($value) || is_numeric($value) || is_bool($value))) {
                    if ($resource->hasAttribute($key)) {
                        $this->assertEquals($value, $resource->$key, "Updated resource field '{$key}' should match request data");
                    }
                }
            }
        }
    }

    protected function assertDeleteOperationResult(string $httpMethod, TestResponse $rs, int $assertStatus, array $requestData, ?Model $resource, ?Model $parent): void
    {
        $idKey = $this->idKey;
        if ($assertStatus === 200 && $resource) {
            // Check that resource was deleted by refetching from database
            $modelClass = get_class($resource);
            $resourceId = $resource->$idKey;
            
            // Try to refetch the resource - it should be gone (hard delete)
            $refetchedResource = $modelClass::find($resourceId);
            $this->assertNull($refetchedResource, 'Resource should be hard deleted from database');
        } else {
            // Operation should have failed - verify resource still exists unchanged
            if ($resource) {
                $modelClass = get_class($resource);
                $resourceId = $resource->$idKey;
                $refetchedResource = $modelClass::find($resourceId);
                $this->assertNotNull($refetchedResource, 'Resource should still exist when delete operation fails');
            }
        }
    }

    protected function assertGetOperationResult(string $httpMethod, TestResponse $rs, int $assertStatus, array $requestData, ?Model $resource, ?Model $parent): void
    {
        if ($assertStatus === 200) {
            // For GET requests, just verify we got some meaningful response
            $json = $rs->json();
            if ($resource) {
                // Single resource view - verify the resource exists in database
                $resource->refresh();
                $this->assertNotNull($resource, 'Requested resource should exist in database');
                
                // Check response has meaningful data
                $this->assertTrue(
                    isset($json['data']) || isset($json['id']) || (is_array($json) && !empty($json)),
                    'GET response should contain meaningful data'
                );
            } else {
                // List view - check for data array or pagination structure  
                $this->assertTrue(
                    isset($json['data']) || isset($json['results']) || is_array($json),
                    'GET list response should contain data array or be an array itself'
                );
            }
        }
    }

    /**
     * Get the model class for this test resource
     */
    protected function getModelClass(): ?string
    {
        // Try to infer model class from the test class name
        $testClass = get_class($this);
        $modelName = str_replace(['Tests\\Feature\\', 'Test'], '', $testClass);
        $modelClass = "App\\Models\\{$modelName}";
        
        return class_exists($modelClass) ? $modelClass : null;
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
