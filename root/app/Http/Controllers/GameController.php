<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\GameResource;
use App\Models\Category;
use App\Models\Game;
use App\Models\User;
use App\Services\APIService;
use Arr;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    /**
     * Edit Game
     *
     * @authenticated
     */
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

        APIService::storeImage($thumbnailFile, 'games/images', $game->thumbnail, 200, fn($path) => $game->thumbnail = $path);
        APIService::storeImage($bannerFile, 'games/images', $game->banner, 200, fn($path) => $game->banner = $path);

        if (!$wasCreated || isset($thumbnailFile) || isset($bannerFile)) {
            $game->update($val);
        }

        return $game;
    }

    /**
     * Create Game
     * 
     * @authenticated
     */
    public function store(Request $request)
    {
        return $this->update($request);
    }

    /**
     * Get List of Games
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

            $query->OrderByRaw('last_date DESC nulls last');
        });

        return GameResource::collection($games);
    }

    /**
     * Get Game
     */
    public function show(Game $game)
    {
        APIService::setCurrentGame($game);

        if (Auth::hasUser()) {
            $game->loadMissing('followed');
            $game->loadMissing('roles');
        }
        return new GameResource($game);
    }

    /**
     * Delete Game
     * 
     * Deletes a game, if it has no mods.
     * 
     * @autehnticated
     */
    public function destroy(Game $game)
    {
        if ($game->mods()->count() == 0) {
            $game->delete();
        }
    }

    /**
     * Set User Roles
     * 
     * @authenticated
     */
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
     * Get Game Data
     * 
     * Returns basic game data like announcements. For moderators, it returns report and waiting mods count.
     */
    public function getGameData(Game $game)
    {
        $data = [
            'announcements' => $game->announcements,
        ];

        if (Auth::hasUser()) {
            $user = Auth::user();
            if ($user->hasPermission('moderate-users', $game)) {
                $data['report_count'] = $game->reportCount;
                $data['waiting_count'] = $game->waitingCount;
            }
        }

        return $data;
    }

    /**
     * Get Game User Data
     * 
     * Returns game data for a user. Currently used for roles.
     */
    public function getGameUserData(Game $game, User $user)
    {
        $roles = $user->getGameRoles($game->id);
        return [
            'role_ids' => array_values(array_unique(Arr::pluck($roles, 'id'))),
            'highest_role_order' => $user->getGameHighestOrder($game->id),
            'ban' => $user->getLastGameban($game->id)
        ];
    }


    /**
     * Get Game User
     *
     * Returns the user as they are supposed to be when inside of a game. Handles roles and colors.
     */
    public function getGameUser(UserController $con, Game $game=null, string $user) {
        return $con->getUser($user, $game);
    }
}
