<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\BaseResource;
use App\Models\Game;
use App\Models\AuditLog;
use App\Models\ModManager;
use App\Services\APIService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * @group Mod Managers
 */
class ModManagerController extends Controller
{
    public function __construct() {
        $this->authorizeResource(ModManager::class, 'mod_manager');
    }

    /**
     * List mod managers
     */
    public function index(FilteredRequest $request, Game $game=null)
    {
        $val = $request->val([
            'global' => 'boolean|nullable',
            'show_hidden' => 'boolean|nullable'
        ]);

        $managers = ModManager::queryGet($val, function($query, array $val) use($game) {
            $user = Auth::user();
            if (isset($game)) {
                $query->where('game_id', $game->id);
            }
            if (isset($val['global']) && $val['global']) {
                $query->orWhereNull('game_id');
            }

            if (!$user?->extra->developer_mode && !($val['show_hidden'] ?? false)) {
                $query->where('hidden', false);
            }
        });

        return BaseResource::collectionResponse($managers);
    }

    /**
     * Create mod manager
     */
    public function store(Request $request, Game $game=null)
    {
        return $this->update($request, null, $game);
    }

    /**
     * Get a mod manager
     */
    public function show(ModManager $modManager)
    {
        return $modManager;
    }

    /**
     * Update a mod manager
     */
    public function update(Request $request, ModManager $modManager = null, Game $game=null)
    {
        $val = $request->validate([
            'name' => 'string|min_strict:3|max:100',
            'download_url' => 'string|max:1000',
            'site_url' => 'url|nullable|max:1000',
            'game_id' => 'integer|min:1|nullable|exists:games,id',
            'hidden' => 'boolean|nullable',
            // 'image_file' => 'nullable|max:512000|mimes:png,webp,avif,gif,jpg',
        ]);

        $val['site_url'] ??= '';

        // $imageFile = Arr::pull($val, 'image_file');

        if (isset($modManager)) {
            AuditLog::logUpdate($modManager, $val);
            $modManager->update($val);
        } else {
            if (isset($game)) {
                $modManager = $game->ownModManagers()->create($val);
            } else {
                $modManager = ModManager::create($val);
            }
            AuditLog::logCreate($modManager, $val);
        }

        // Temporary unused
        // APIService::storeImage($imageFile, 'mod-managers', $modManager->image, null, fn($path) => $modManager->image = $path);
        $modManager->save();

        return $modManager;
    }

    /**
     * Delete a mod manager
     */
    public function destroy(ModManager $modManager)
    {
        $modManager->delete();
    }
}
