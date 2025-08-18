<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Models\IgnoredGame;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

/**
 * @group Users
 *
 * @subgroup Ignored Games
 *
 * @authenticated
 */
class IgnoredGameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List ignored games
     */
    public function index(Request $request)
    {
        $val = $request->validate([
            'limit' => 'integer|min:1|max:50'
        ]);

        return GameResource::collectionResponse($this->user()->ignoredGames()->queryGet($val));
    }

    /**
     * Create an ignored game
     */
    public function store(Request $request, Authenticatable $user)
    {
        $val = $request->validate([
            'game_id' => 'int|min:0|exists:games,id',
        ]);

        $userId = $user->id;
        if (IgnoredGame::where('user_id', $userId)->where('game_id', $val['game_id'])->exists()) {
            abort(409, 'Already ignoring game');
        }

        IgnoredGame::create(['game_id' => $val['game_id'], 'user_id' => $userId]);
    }

    /**
     * Delete an ignored game from ignored games
     */
    public function destroy(int $id)
    {
        $this->user()->ignoredGames()->detach($id);
    }
}
