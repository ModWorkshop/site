<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetModsRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\ModResource;
use App\Models\FollowedGame;
use App\Models\Mod;
use App\Models\User;
use App\Services\ModService;
use DB;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Users
 *
 * @subgroup Followed Games
 *
 * @authenticated
 */
class FollowedGameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get List of Followed Games
     */
    public function index(Request $request)
    {
        $val = $request->validate([
            'limit' => 'integer|min:1|max:50'
        ]);

        return GameResource::collectionResponse($this->user()->followedGames()->queryGet($val));
    }

    /**
     * Get List of Followed Games Mods
     */
    public function mods(GetModsRequest $request, Authenticatable $user)
    {
        $val = $request->val();
        $mods = Mod::queryGet($val, function($query, $val) use ($user) {
            $query->whereExists(function($query) use ($user) {
                $query->from('followed_games')->select(DB::raw(1))->where('user_id', $user->id);
                $query->whereColumn('followed_games.game_id', 'mods.game_id');
            });

            ModService::filters($query, $val);
        }, true);
        return ModResource::collectionResponse($mods);
    }

    /**
     * Add Followed Game
     */
    public function store(Request $request, Authenticatable $user)
    {
        $val = $request->validate([
            'game_id' => 'int|min:0|exists:games,id',
        ]);

        $userId = $user->id;
        if (FollowedGame::where('user_id', $userId)->where('game_id', $val['game_id'])->exists()) {
            abort(409, 'Already following game');
        }

        FollowedGame::create(['game_id' => $val['game_id'], 'user_id' => $userId]);
    }

    /**
     * Delete Followed Game
     */
    public function destroy(int $id)
    {
        $this->user()->followedGames()->detach($id);
    }
}
