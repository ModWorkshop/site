<?php

namespace Tests;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;

trait TestOwnershipTrait {
    /**
     * Properties that should be available in classes using this trait
     * These should be declared in the class using this trait:
     * - protected bool $isGlobal
     * - protected Model $parent
     */

    /**
     * Methods that should be available in classes using this trait
     */
    abstract protected function user(): User;
    abstract protected function getUserForScenario(string $userType): ?User;

    /**
     * Data provider for ownership scenarios
     */
    public static function ownershipScenariosProvider(): array
    {
        return [
            'other_user_cannot_modify' => ['other_user', 'other_user', 403],
            'admin_can_modify_any' => ['admin', 'admin', 200],
        ];
    }
  
    protected function _testOwnershipScenarios(string $operation, ?string $userType, int $expectedStatus, ?int $expectedParentedStatus = null): void {
        $user = $userType ? $this->getUserForScenario($userType) : null;
        $otherUser = $this->user();

        // Test global context if applicable
        if ($this->isGlobal) {
            $this->testScenario($operation, [ 
                'assertStatus' => $expectedStatus,
                'user' => $user,
                'owner' => $otherUser,
                'userType' => $userType,
            ]);
        }

        // Test game context if applicable
        if ($this->hasParent) {
            $this->testScenario($operation, [ 
                'assertStatus' => $expectedParentedStatus ?? $expectedStatus,
                'user' => $user,
                'userType' => $userType,
                'owner' => $otherUser,
                'parent' => $this->getParent($otherUser),
            ]);
        }
    }

    /**
     * Test update ownership scenarios
     */
    #[DataProvider('ownershipScenariosProvider')]
    public function test_update_ownership(string $scenarioName, ?string $userType, int $expectedStatus, ?int $expectedGameStatus=null): void
    {
        $this->_testOwnershipScenarios('update', $userType, $expectedStatus, $expectedGameStatus);
    }

    /**
     * Test delete ownership scenarios
     */
    #[DataProvider('ownershipScenariosProvider')]
    public function test_delete_ownership(string $scenarioName, ?string $userType, int $expectedStatus, ?int $expectedGameStatus=null): void
    {
        $this->_testOwnershipScenarios('delete', $userType, $expectedStatus, $expectedGameStatus);
    }
}