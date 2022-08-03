<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Game;
use App\Services\APIService;
use Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

/**
 * @group Games
 * 
 * API routes for interacting with game sections.
 */
class GameController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Game::class, 'game');
    }

    public function update(Request $request, Game $game=null)
    {
        $val = $request->validate([
            'name' => 'string|max:150',
            'buttons' => 'string|max:1000',
            'thumbnail_file' => 'nullable|max:512000|mimes:png,webp,gif,jpg',
            'banner_file' => 'nullable|max:512000|mimes:png,webp,gif,jpg',
            'short_name' => 'string|nullable|max:30',
            'webhook_url' => 'string|nullable|max:1000',
        ]);

        $val['webhook_url'] ??= '';
        $val['buttons'] ??= '';

        $thumbnailFile = Arr::pull($val, 'thumbnail_file');
        $bannerFile = Arr::pull($val, 'banner_file');

        $wasCreated = false;
        if (!isset($game)) {
            $val['last_date'] = Date::now();
            /**
             * @var Game
             */
            $game = Game::create($val);
            $val = [];//Empty so we don't update it again.
            $wasCreated = true;
        }

        APIService::tryUploadFile($thumbnailFile, 'games/thumbnails', $game->thumbnail, fn($path) => $game->thumbnail = $path);
        APIService::tryUploadFile($bannerFile, 'games/banners', $game->banner, fn($path) => $game->banner = $path);

        if (!$wasCreated || isset($thumbnailFile) || isset($bannerFile)) {
            $game->update($val);
        }

        return $game;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request);
    }

    /**
     * Mod Cateogries
     *
     * @param Request $request
     * @param Category|null $game
     * @return void
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val([
            //Returns only the names of the categories
            'only_names' => 'boolean',
            'include_paths' => 'boolean'
        ]);

        $games = Game::queryGet($val, function(Builder $query, array $val) {
            if (($val['only_names'] ?? false)) {
                $query->select(['id', 'name']);
            }

            $query->orderBy('name');
        });

        return CategoryResource::collection($games);
    }

    public function show(Game $game)
    {
        return new CategoryResource($game);
    }

    public function delete()
    {
        
    }

    public function getGame(string|int $shortNameOrId)
    {
        $game = null;

        if (is_numeric($shortNameOrId)) {
            $game = Game::find($shortNameOrId);
        } else {
            $game = Game::where('short_name', $shortNameOrId)->first();
        }
        return $this->show($game);
    }
}
