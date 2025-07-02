<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use App\Models\Mod;
use App\Models\Model;
use Tests\UserResourceTest;

class ReportTest extends UserResourceTest
{
    protected string $parentUrl = '';
    protected string $url = 'reports';
    protected bool $isGlobal = true;
    protected bool $isGame = false;

    public function makeParent(): void
    {
        
    }

    public function createDummy(?User $user = null, ?Model $parent = null): ?Report
    {
        $user ??= $this->user();
        
        // Create a mod to report (since reports need a reportable item)
        $mod = Mod::factory()->create([
            'user_id' => $this->user()->id,
            'game_id' => $this->game->id,
        ]);
        
        return Report::create([
            'user_id' => $user->id,
            'reportable_type' => 'mod',
            'reportable_id' => $mod->id,
            'reason' => 'This is a test report for API testing',
            'data' => []
        ]);
    }

    public function getUrl($httpMethod, ?Model $parent = null, ?Model $object = null, $data=null): string {
        if ($httpMethod == 'POST') {
            return $data['reportable_type']."s/{$data['reportable_id']}/reports";
        } else {
            return parent::getUrl($httpMethod, $parent, $object, $data);
        }
    }

    public function upsertData(?Model $parent): array
    {
        // Create a mod to report
        $mod = Mod::factory()->create([
            'user_id' => $this->user()->id,
            'game_id' => $this->game->id,
            'has_download' => true,
            'published_at' => now()
        ]);

        return [
            'reportable_type' => 'mod',
            'reportable_id' => $mod->id,
            'reason' => 'This mod appears to be spam content',
            'archived' => false
        ];
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
