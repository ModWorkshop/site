<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Ban;
use App\Models\Game;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserCase;
use App\Services\Utils;
use Arr;
use Illuminate\Http\Response;
use Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;

/**
 * @group User Cases
 * @authenticated
 *
 * @hideFromApiDocumentation
 */
class UserCaseController extends Controller
{
    public function __construct(Request $request)
    {
        $this->authorizeGameResource(UserCase::class);
    }

    /**
     * User Cases
     *
     * Returns user cases (warnings) that the user has
     */
    public function index(Request $request, Game $game=null)
    {
        $val = $request->validate([
            'user_id' => 'int|min:0|nullable|exists:users,id',
            'all' => 'boolean|nullable'
        ]);

        return BaseResource::collectionResponse(UserCase::with('modUser')->queryGet($val, function($q, $val) use($game) {
            if (isset($game)) {
                $q->where('game_id', $game->id);
            } else {
                if (!($val['all'] ?? false)) {
                    $q->whereNull('game_id');
                }
            }

            if (isset($val['user_id'])) {
                $q->where('user_id', $val['user_id']);
            }

            $q->orderByRaw('active DESC, created_at DESC');
        }));
    }

    /**
     * Create User Case
     */
    public function store(Request $request, Game $game=null)
    {
        $val = $request->validate([
            'user_id' => 'int|min:0|exists:users,id',
            'reason' => 'string|min:3|max:1000',
            'expire_date' => 'date|nullable|after:now'
        ]);

        Utils::convertToUTC($val, 'expire_date');

        if (isset($game)) {
            $val['game_id'] = $game->id;
        }
        $val['active'] = true;
        $val['mod_user_id'] = $this->userId();

        Utils::convertToUTC($val, 'expire_date');

        $user = User::find($val['user_id']);
        $case = UserCase::create($val);

        Notification::send(
            notifiable: $case,
            user: $user,
            hideSender: true,
            type: 'warning'
        );

        $case->load('user');
        $case->load('modUser');
        return $case;
    }

    /**
     * Get User Case
     */
    public function show(UserCase $userCase)
    {
        return $userCase;
    }

    /**
     * Edit User Case
     */
    public function update(Request $request, UserCase $userCase)
    {
        $val = $request->validate([
            'reason' => 'string|min:3|max:1000',
            'expire_date' => 'date|nullable',
            'active' => 'boolean',
        ]);

        Utils::convertToUTC($val, 'expire_date');

        $userCase->update($val);

        return $userCase;
    }

    /**
     * Delete User Case
     */
    public function destroy(UserCase $userCase)
    {
        $userCase->delete();
    }
}
