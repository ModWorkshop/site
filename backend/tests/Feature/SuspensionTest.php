<?php

namespace Tests\Feature;

use App\Models\Model;
use App\Models\Suspension;
use App\Models\Mod;
use App\Models\User;
use Tests\AdminResourceTest;

class SuspensionTest extends AdminResourceTest
{
    protected string $parentUrl = 'games';
    protected string $url = 'suspensions';
    protected bool $isGlobal = true;
    protected bool $hasParent = true;

    public function createDummy(?User $user = null, ?Model $parent = null): ?Suspension
    {
        $user ??= $this->admin(); // Only admins can create suspensions
        // Create a mod to suspend
        $targetUser = $this->user();
        $mod = Mod::create([
            'name' => 'Test Mod for Suspension',
            'desc' => 'This is a test mod for suspension testing',
            'user_id' => $targetUser->id,
            'game_id' => $parent?->id ?? $this->game->id,
            'visibility' => 'public',
        ]);
        
        return Suspension::create([
            'mod_id' => $mod->id,
            'reason' => 'Test suspension reason',
            'mod_user_id' => $user->id,
            // 'game_id' => $mod->game_id,
        ]);
    }

    public function getUrl($httpMethod, ?Model $parent = null, ?Model $object = null, $data=null): string {
        if ($httpMethod == 'POST') {
            return "mods/{$data['mod_id']}/suspensions";
        } else {
            return parent::getUrl($httpMethod, $parent, $object, $data);
        }
    }

    public function upsertData(?Model $parent, string $method): array
    {
        if ($method === 'POST') {
            $mod = Mod::create([
                'name' => 'Another Test Mod for Suspension',
                'desc' => 'Another test mod for suspension testing',
                'user_id' => $this->user()->id,
                'game_id' => $parent?->id,
                'visibility' => 'public',
            ]);
            return [
                'mod_id' => $mod->id,
                'reason' => 'This mod appears to be spam content',
                'status' => true
            ];
        } else {
            return [
                'reason' => 'Updated suspension reason for API testing',
                'status' => false
            ];
        }
    }

    /**
     * Suspensions are typically only viewable by admins and moderators
     */
    public static function viewScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403], // Suspensions should be private
            'unverified' => ['unverified', 'unverified', 403],
            'verified' => ['verified', 'verified', 403],
            'banned' => ['banned', 'banned', 403],
            'game_banned' => ['game_banned', 'game_banned', 403],
            'admin' => ['admin', 'admin', 200], // Only admins can view suspensions
        ];
    }

    /**
     * Only admins can create suspensions
     */
    public static function createScenariosProvider(): array
    {
        return [
            'guest' => ['guest', null, 403, 403],
            'unverified' => ['unverified', 'unverified', 403, 403],
            'verified' => ['verified', 'verified', 403, 403], // Regular users can't suspend content
            'banned' => ['banned', 'banned', 403, 403],
            'game_banned' => ['game_banned', 'game_banned', 403, 403],
            'admin' => ['admin', 'admin', 201, 201], // Only admins can suspend content
        ];
    }
}
