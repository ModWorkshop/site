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
use App\Models\AuditLog;
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
     * List bans
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
     * Create a ban
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

        AuditLog::log('ban', $banUser, [
            'with' => [
                'expire_date' => $val['expire_date'] ?? null,
                'reason' => $val['reason'],
                'can_appeal' => $val['can_appeal'] ?? false
            ]
        ], $game);

        return $ban;
    }

    /**
     * Update a ban
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

        AuditLog::logUpdate($ban, $val, objectUserAsContext: true);

        $ban->update($val);

        return $ban->load(['user']);
    }

    /**
     * Get a ban
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Ban $ban)
    {
        $ban->load('user');
        return $ban;
    }

    /**
     * Delete a ban
     */
    public function destroy(Ban $ban)
    {
        AuditLog::logDelete($ban, objectUserAsContext: true);
        $ban->delete();
    }
}
