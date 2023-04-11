<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Ban;
use App\Models\Game;
use App\Models\User;
use Arr;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Log;

class BanController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Ban::class, 'ban');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request, Game $game=null)
    {
        $val = $request->val([
            'user_id' => 'int|min:1|nullable|exists:users,id',
            'limit' => 'integer|min:1|max:1000',
        ]);
        
        return JsonResource::collection(Ban::queryGet($val, function($query) use ($game, $val) {
            if (isset($game)) {
                $query->where('game_id', $game->id);
            } else {
                $query->whereNull('game_id');
            }

            if (isset($val['user_id'])) {
                $query->where('user_id', $val['user_id']);
            }
            $query->with('user');
            $query->with('modUser');
            $query->orderByRaw('active DESC, expire_date DESC');
        }));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Game $game=null)
    {
        $val = $request->validate([
            'user_id' => 'int|min:1|required|exists:users,id',
            'expire_date' => 'date|after:now|nullable',
            'reason' => 'string|min:3|max:1000',
            'can_appeal' => 'boolean|nullable'
        ]);

        /** @var User */
        $banUser = User::find($val['user_id']);

        if (!$banUser->canBeEdited()) {
            abort(403, 'Cannot ban user');
        }

        # Deactivate existing ban, allowing moderators to more easily update bans.
        if (isset($game)) {
            $banUser->gameBan?->deactivate();
        } else {
            $banUser->ban?->deactivate();
        }

        $gameId = $game?->id;

        if (isset($gameId)) {
            $val['game_id'] = $gameId;
        }

        $val['active'] = true;
        $val['mod_user_id'] = Auth::getUser()->id;

        $ban = Ban::create($val);
        $ban->load('user');

        
        return $ban;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ban $ban)
    {
        $val = $request->validate([
            'expire_date' => 'date|after:now|nullable',
            'reason' => 'string|min:3|max:1000',
            'can_appeal' => 'boolean|nullable',
        ]);

        if (!$ban->user->canBeEdited()) {
            abort(403, 'Cannot edit ban of user since you cannot ban them normally.');
        }

        $ban->update($val);

        return $ban->load(['user']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Ban $ban)
    {
        return $ban;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ban $ban)
    {
        $ban->delete();
    }
}
