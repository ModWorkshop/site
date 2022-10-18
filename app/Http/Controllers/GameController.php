<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\RoleResource;
use App\Models\Category;
use App\Models\Game;
use App\Models\User;
use App\Services\APIService;
use Arr;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Spatie\QueryBuilder\QueryBuilder;

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
            'buttons' => 'nullable|string|max:1000',
            'thumbnail_file' => 'nullable|max:512000|mimes:png,webp,gif,jpg',
            'banner_file' => 'nullable|max:512000|mimes:png,webp,gif,jpg',
            'short_name' => 'string|nullable|max:30',
            'webhook_url' => 'string|nullable|max:1000',
        ]);

        APIService::nullToEmptyStr($val, 'webhook_url', 'buttons');

        $thumbnailFile = Arr::pull($val, 'thumbnail_file');
        $bannerFile = Arr::pull($val, 'banner_file');

        $wasCreated = false;
        if (!isset($game)) {
            $val['last_date'] = Date::now();
            /** @var Game */
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

        $games = QueryBuilder::for(Game::class)->allowedIncludes(['roles'])->queryGet($val, function(Builder $query, array $val) {
            if (($val['only_names'] ?? false)) {
                $query->select(['id', 'name']);
            }

            $query->orderBy('last_date');
        });

        return GameResource::collection($games);
    }

    public function show(Game $game)
    {
        if (Auth::hasUser()) {
            $game->loadMissing('followed');
        }
        APIService::setCurrentGame($game);
        return $game;
    }

    public function delete()
    {
        
    }

    public function setUserGameRoles(Request $request, Game $game, User $user)
    {
        $this->authorize('manageRoles', [$game, $user]);

        $val = $request->validate([
            'role_ids' => 'array',
            'role_ids.*' => 'integer|min:1|exists:game_roles,id',
        ]);

        $user->syncGameRoles($game, array_map('intval', array_filter($val['role_ids'], fn($val) => is_numeric($val))));
    }

    /**
     * Returns game data for a user. Currently used for roles.
     */
    public function getGameUserData(Game $game, User $user)
    {
        $roles = $user->getGameRoles($game->id);
        return [
            'role_ids' => array_values(array_unique(Arr::pluck($roles, 'id'))),
            'highest_role_order' => $user->getGameHighestOrder($game->id),
        ];
    }
}
