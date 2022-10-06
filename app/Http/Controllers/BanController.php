<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Ban;
use App\Models\Game;
use App\Models\User;
use App\Models\UserCase;
use Arr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
        $val = $request->validate([
            'user_id' => 'int|min:0|nullable|exists:users,id',
            'limit' => 'integer|min:1|max:1000',
        ]);
        
        return JsonResource::collection(Ban::queryGet($val, function($query) use ($game) {
            if (isset($game)) {
                $query->where('game_id', $game->id);
            } else {
                $query->whereNull('game_id');
            }

            $query->with('user');
            $query->with('case.modUser');
            $query->where(fn($q) => $q->where('expire_date', '>', Carbon::now())->orWhereNull('expire_date'));
            if (isset($val['user_id'])) {
                $query->where('user_id', $val['user_id']);
            }
        }));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Game $game)
    {
        $val = $request->validate([
            'user_id' => 'int|min:1|required|exists:users,id',
            'expire_date' => 'date|after:now|nullable',
            'reason' => 'string|min:3|max:1000'
        ]);

        $user = $request->user();
        /**
         * @var User
         */
        $banUser = User::find($val['user_id']);
        
        if (isset($game)) {
            if ($banUser->gameBan) {
                abort(405, 'Already banned'); //Already banned
            }
        } else {
            if ($banUser->lastBan) {
                abort(405, 'Already banned'); //Already banned
            }
        }

        if (!$banUser->canBeEdited($user)) {
            abort(403, 'Cannot ban user');
        }

        $reason = Arr::pull($val, 'reason');
        $gameId = $game?->id;

        $case = UserCase::create([
            'warning' => false,
            'reason' => $reason,
            'game_id' => $gameId,
            'user_id' => $val['user_id'],
            'mod_user_id' => $this->userId(),
            'expire_date' => $val['expire_date']
        ]);

        if (isset($gameId)) {
            $val['game_id'] = $gameId;
        }
        $val['case_id'] = $case->id;

        $ban = Ban::create($val);

        return $ban->load(['user', 'case']);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ban $ban)
    {
        
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
