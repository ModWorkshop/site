# Improved TestResource Base Class

The `TestResource` class has been enhanced to provide a robust, comprehensive testing framework for API endpoints with different user roles, permissions, and scenarios.

## Features

### 1. **Role-Based Testing**
The class automatically tests various user types:
- `guest` - Unauthenticated users
- `unverified` - Users with unverified email addresses
- `verified` - Regular verified users
- `banned` - Globally banned users
- `game_banned` - Users banned from specific games
- `admin` - Administrator users
- `other_user` - Different user (for ownership testing)

### 2. **Comprehensive Test Scenarios**
Each scenario defines expected HTTP status codes for:
- `createExpected` - POST requests
- `updateExpected` - PATCH/PUT requests
- `deleteExpected` - DELETE requests
- `viewExpected` - GET requests

### 3. **Enhanced Error Reporting**
- Detailed debug information when assertions fail
- Request/response logging
- User context information
- Backtrace for debugging

### 4. **Ownership Testing**
- Tests owner vs non-owner access
- Admin override scenarios
- Permission inheritance

## Usage

### Basic Implementation

```php
<?php

namespace Tests\Feature;

use App\Models\YourModel;
use App\Models\User;
use Tests\TestResource;

class YourModelTest extends TestResource
{
    protected string $parentUrl = 'games';  // Parent resource URL
    protected string $url = 'your-models';  // Resource URL
    protected bool $isGlobal = false;       // Whether it's a global resource
    protected bool $hasParent = true;          // Whether it belongs to a game

    public function createDummy(?User $user = null, ?int $parentId = null): ?YourModel
    {
        $user ??= $this->user();
        $parentId ??= $this->parent->id;
        
        return YourModel::create([
            'user_id' => $user->id,
            'parent_id' => $parentId,
            'name' => 'Test Resource',
            'description' => 'Test Description',
        ]);
    }

    public function upsertData(): array
    {
        return [
            'name' => 'Test Name',
            'description' => 'Test Description',
        ];
    }
}
```

### Customizing Test Scenarios

```php
protected function configureTestScenarios(): void
{
    parent::configureTestScenarios();
    
    // Override default expectations
    $this->testScenarios['verified']['createExpected'] = 403; // Verified users can't create
    
    // Add custom scenarios
    $this->testScenarios['moderator'] = [
        'user' => 'moderator',
        'createExpected' => 201,
        'updateExpected' => 200,
        'deleteExpected' => 200,
        'viewExpected' => 200,
    ];
}

protected function getUserForScenario(string $userType): ?User
{
    if ($userType === 'moderator') {
        $user = $this->user();
        // Add moderator role/permissions
        return $user;
    }
    
    return parent::getUserForScenario($userType);
}
```

### Running Tests

The base class provides these test methods:

```php
public function test_create()
{
    $this->runTestScenarios('create');
}

public function test_update()
{
    $this->runTestScenarios('update');
    $this->testOwnershipScenarios('update');
}

public function test_delete()
{
    $this->runTestScenarios('delete');
    $this->testOwnershipScenarios('delete');
}

public function test_view()
{
    $this->runTestScenarios('view');
}
```

### Custom Tests

You can still add custom test methods:

```php
public function test_custom_functionality(): void
{
    $user = $this->user();
    $resource = $this->createDummy($user);
    
    $this->actingAs($user);
    $response = $this->postJson("/your-models/{$resource->id}/custom-action");
    
    $response->assertStatus(200);
}
```

## Key Methods

### Core Testing Methods
- `tryCreate($status, $user, $parentId)` - Test resource creation
- `tryUpdate($status, $user, $actingAs, $parentId)` - Test resource updates
- `tryDelete($status, $user, $actingAs, $parentId)` - Test resource deletion
- `tryView($status, $user, $parentId)` - Test resource viewing

### Helper Methods
- `runTestScenarios($operation)` - Run all configured scenarios for an operation
- `testOwnershipScenarios($operation)` - Test ownership-based permissions
- `getUserForScenario($userType)` - Get user instance for scenario type
- `assertStatusCodeWithDebug()` - Enhanced assertion with debug info

### Required Implementations
Child classes must implement:
- `createDummy(?User $user, ?int $parentId): ?Model` - Create test resource
- `upsertData(): array` - Return data for create/update requests

### Optional Overrides
- `configureTestScenarios()` - Customize test scenarios
- `makeParent()` - Set up parent resource
- `getUserForScenario($userType)` - Add custom user types

## Benefits

1. **Consistency** - All API endpoints tested with same scenarios
2. **Comprehensive** - Covers all user types and permissions
3. **Maintainable** - Central configuration for test expectations
4. **Debuggable** - Enhanced error reporting and logging
5. **Extensible** - Easy to add custom scenarios and user types
6. **Automated** - Reduces boilerplate test code

## Migration from Old Tests

To migrate existing tests:

1. Extend `TestResource` instead of `TestCase`
2. Implement `createDummy()` and `upsertData()` methods
3. Configure `$url`, `$parentUrl`, `$isGlobal`, and `$isGame` properties
4. Remove old test methods and let base class handle them
5. Add custom scenarios if needed

The improved TestResource provides a solid foundation for comprehensive API testing with minimal setup required per resource type.
