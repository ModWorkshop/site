<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\BaseResource;
use App\Models\Game;
use App\Models\ModManager;
use App\Services\APIService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ModManagerController extends Controller
{
    public function __construct() {
        $this->authorizeResource(ModManager::class, 'mod_manager');
    }

    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Game $game=null)
    {
        return $this->update($request, null, $game);
    }

    /**
     * Display the specified resource.
     */
    public function show(ModManager $modManager)
    {
        return $modManager;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModManager $modManager = null, Game $game=null)
    {
        $val = $request->validate([
            'name' => 'string|min:3|max:100',
            'download_url' => 'string|max:1000',
            'site_url' => 'url|nullable|max:1000',
            'game_id' => 'integer|min:1|nullable|exists:games,id',
            'hidden' => 'boolean|nullable',
            // 'image_file' => 'nullable|max:512000|mimes:png,webp,gif,jpg',
        ]);

        $val['site_url'] ??= '';

        // $imageFile = Arr::pull($val, 'image_file');

        if (isset($modManager)) {
            $modManager->update($val);
        } else {
            if (isset($game)) {
                $modManager = $game->ownModManagers()->create($val);
            } else {
                $modManager = ModManager::create($val);
            }
        }

        // Temporary unused
        // APIService::storeImage($imageFile, 'mod-managers', $modManager->image, null, fn($path) => $modManager->image = $path);
        $modManager->save();

        return $modManager;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModManager $modManager)
    {
        $modManager->delete();
    }
}
