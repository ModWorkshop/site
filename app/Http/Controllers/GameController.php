<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Requests\GetModsRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\ModResource;
use App\Models\Category;
use App\Models\Game;
use App\Models\ModManager;
use App\Models\User;
use App\Services\APIService;
use App\Services\ModService;
use Arr;
use Auth;
use Cache;
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
     * Update a game
     *
     * @authenticated
     */
    public function update(Request $request, Game $game=null)
    {
        $validateArr = [
            'name' => 'string|max:150',
            'buttons' => 'nullable|string|max:1000',
            'thumbnail_file' => 'nullable|max:512000|mimes:png,webp,avif,gif,jpg',
            'banner_file' => 'nullable|max:512000|mimes:png,webp,avif,gif,jpg',
            'short_name' => 'string|nullable|max:30',
            'webhook_url' => 'string|nullable|max:1000',
            'mod_manager_ids' => 'array|nullable',
            'mod_manager_ids.*' => 'integer|min:1|exists:mod_managers,id|nullable',
            'default_mod_manager_id' => 'exists:mod_managers,id|nullable'
        ];

        if (!isset($game)) {
            $validateArr['short_name'] = 'string|max:30';
        }

        $val = $request->validate($validateArr);

        APIService::nullToEmptyStr($val, 'webhook_url', 'buttons');

        $thumbnailFile = Arr::pull($val, 'thumbnail_file');
        $bannerFile = Arr::pull($val, 'banner_file');
        $modManagerIds = Arr::pull($val, 'mod_manager_ids');

        $wasCreated = false;
        if (!isset($game)) {
            $val['last_date'] = Date::now();
            /** @var Game */
            $game = Game::create($val);
            $forum = $game->forum;


            $val = [];//Empty so we don't update it again.
            $wasCreated = true;
        }

        if (isset($modManagerIds)) {
            $modManagers = ModManager::whereIn('id', $modManagerIds)->get();
            $modManagerIds = [];
            foreach ($modManagers as $manager) {
                if (!empty($manager->game_id)) {
                    abort(406, 'You cannot add mod managers that a game owns.');
                }

                $modManagerIds[] = $manager->id;
            }
            $game->modManagers()->sync($modManagerIds);
        }

        APIService::storeImage($thumbnailFile, 'games/images', $game->thumbnail, [
            'thumbnailSize' => 200,
            'onSuccess' => fn($path) => $game->thumbnail = $path,
        ]);

        APIService::storeImage($bannerFile, 'games/images', $game->banner, [
            'thumbnailSize' => 200,
            'onSuccess' => fn($path) => $game->banner = $path,
        ]);

        if (!$wasCreated || isset($thumbnailFile) || isset($bannerFile)) {
            $game->update($val);
        }

        return $game;
    }

    /**
     * Get game section data
     *
     * It's like /mods but returns the game too. It's used to avoid 2 requests in the game section so it's faster.
     */
    function gameSectionData(GetModsRequest $request, Game $game) {
        /** @var \Illuminate\Pagination\LengthAwarePaginator $mods */
        $mods = ModService::mods(val: $request->val(), query: $game->mods()->without('game'), cacheForGuests: $game->short_name.'-index');
        $game->load('categories');
        $game = $this->show($game);

        return [
            'mods' => ModResource::collectionResponse($mods),
            'game' => $game
        ];
    }

    /**
     * Create a game
     *
     * @authenticated
     */
    public function store(Request $request)
    {
        return $this->update($request);
    }

    /**
     * List games
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val([
            //Returns only the names of the categories
            'only_names' => 'boolean',
            'include_paths' => 'boolean'
        ]);


        $games = QueryBuilder::for(Game::class)->allowedIncludes(['roles'])->queryGet($val, function(Builder $query, array $val) {
            $query->withCount('viewableMods');
            if (($val['only_names'] ?? false)) {
                $query->select(['id', 'name']);
            }

            $query->OrderByRaw('last_date DESC nulls last');
        });

        return GameResource::collectionResponse($games);
    }

    /**
     * Get a game
     */
    public function show(Game $game)
    {
        APIService::setCurrentGame($game);

        $game->loadCount('viewableMods');
        $game->loadMissing('modManagers');

        if (Auth::hasUser()) {
            $game->loadMissing('followed');
            $game->loadMissing('roles');
        }
        return new GameResource($game);
    }

    /**
     * Delete a game
     *
     * Deletes a game, if it has no mods.
     *
     * @autehnticated
     * @hideFromApiDocumentation
     */
    public function destroy(Game $game)
    {
        if ($game->mods()->count() == 0) {
            $game->delete();
        }
    }

    /**
     * Set user roles
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
     * Get game data
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
     * Get game user data
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
     * Get a game user
     *
     * Returns the user as they are supposed to be when inside of a game. Handles roles and colors.
     */
    public function getGameUser(UserController $con, Game $game=null, string $user) {
        return $con->getUser($user, $game);
    }

    /**
     * @hideFromApiDocumentation
     */
    public function getAdminData(Game $game)
    {
        return APIService::adminData($game);
    }
}
