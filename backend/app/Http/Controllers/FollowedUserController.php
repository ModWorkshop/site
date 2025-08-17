<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Requests\GetModsRequest;
use App\Http\Resources\ModResource;
use App\Http\Resources\UserResource;
use App\Models\FollowedUser;
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
 * @subgroup Followed Users
 *
 * @authenticated
 */
class FollowedUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List follwoed users
     */
    public function index(FilteredRequest $request)
    {
        return UserResource::collectionResponse($this->user()->followedUsers()->queryGet($request->val()));
    }

    /**
     * List followed users mods
     *
     * @param GetModsRequest $request
     * @param Authenticatable $user
     * @return void
     */
    public function mods(GetModsRequest $request, Authenticatable $user)
    {
        $val = $request->val();
        $mods = ModService::mods($val, function($query, $val) use ($user) {
            $query->whereExists(function($query) use ($user) {
                $query->from('followed_users')->select(DB::raw(1))->where('user_id', $user->id);
                $query->whereColumn('followed_users.follow_user_id', 'mods.user_id');
            });

            ModService::filters($query, $val);
        });
        return ModResource::collectionResponse($mods);
    }

    /**
     * Create a followed user
     */
    public function store(Request $request, Authenticatable $user)
    {
        $val = $request->validate([
            'follow_user_id' => 'int|min:1|exists:users,id|required',
            'notify' => 'boolean'
        ]);

        $userId = $user->id;
        $notify = $val['notify'] ?? false;
        $followedUserId = $val['follow_user_id'];

        $followedUser = FollowedUser::where('user_id', $userId)->where('follow_user_id', $followedUserId)->first();

        if (isset($followedUser)) {
            $followedUser->update(['follow_user_id' => $followedUserId, 'notify' => $notify]);
        } else {
            FollowedUser::create(['follow_user_id' => $followedUserId, 'user_id' => $userId, 'notify' => $notify]);
        }
    }

    /**
     * Delete a followed user
     */
    public function destroy(int $id, Authenticatable $user)
    {
        FollowedUser::where('user_id', $user->id)->where('follow_user_id', $id)->delete();
    }
}
