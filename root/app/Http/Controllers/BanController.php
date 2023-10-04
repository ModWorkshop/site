<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Ban;
use App\Models\Game;
use App\Models\User;
use App\Services\Utils;
use Arr;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Response;
use Log;

/**
 * @group Bans
 * @authenticated
 * @hideFromApiDocumentation
 */
class BanController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Ban::class);
    }

    /**
     * Get List of Bans
     */
    public function index(FilteredRequest $request, Game $game=null)
    {
        $val = $request->val([
            'user_id' => 'int|min:1|nullable|exists:users,id',
            'limit' => 'integer|min:1|max:1000',
        ]);

        return BaseResource::collectionResponse(Ban::queryGet($val, function($query) use ($game, $val) {
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
            $query->orderByRaw('active DESC, created_at DESC');
        }));
    }

    /**
     * Create Ban
     */
    public function store(Request $request, Game $game=null)
    {
        $val = $request->validate([
            'user_id' => 'int|min:1|required|exists:users,id',
            'expire_date' => 'date|after:now|nullable',
            'reason' => 'string|min:3|max:1000',
            'can_appeal' => 'boolean|nullable'
        ]);

        Utils::convertToUTC($val, 'expire_date');

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
     * Edit Ban
     */
    public function update(Request $request, Ban $ban)
    {
        $val = $request->validate([
            'expire_date' => 'date|after:now|nullable',
            'reason' => 'string|min:3|max:1000',
            'can_appeal' => 'boolean|nullable',
        ]);

        Utils::convertToUTC($val, 'expire_date');

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
     * @return Response
     */
    public function show(Ban $ban)
    {
        return $ban;
    }

    /**
     * Delete Ban
     */
    public function destroy(Ban $ban)
    {
        $ban->delete();
    }
}
