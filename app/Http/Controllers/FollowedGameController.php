<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetModsRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\ModResource;
use App\Models\FollowedGame;
use App\Models\Mod;
use App\Services\ModService;
use DB;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class FollowedGameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $val = $request->validate([
            'limit' => 'integer|min:1|max:50'
        ]);

        return GameResource::collection($this->user()->followedGames()->queryGet($val));
    }

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
        return ModResource::collection($mods);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Authenticatable $user)
    {
        $val = $request->validate([
            'game_id' => 'int|min:0|exists:games,id',
        ]);

        $userId = $user->id;
        if (FollowedGame::where('user_id', $userId)->where('game_id', $val['game_id'])->exists()) {
            abort(409);
        }

        FollowedGame::create(['game_id' => $val['game_id'], 'user_id' => $userId]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $this->user()->followedGames()->where('game_id', $id)->delete();
    }
}
