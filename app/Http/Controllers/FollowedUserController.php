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

class FollowedUserController extends Controller
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
    public function index(FilteredRequest $request)
    {
        return UserResource::collection($this->user()->followedUsers()->queryGet($request->val()));
    }

    public function mods(GetModsRequest $request, Authenticatable $user)
    {
        $val = $request->val();
        $mods = Mod::queryGet($val, function($query, $val) use ($user) {
            $query->whereExists(function($query) use ($user) {
                $query->from('followed_users')->select(DB::raw(1))->where('user_id', $user->id);
                $query->whereColumn('followed_users.follow_user_id', 'mods.user_id');
            });

            ModService::filters($query, $val);
        });
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
            'user_id' => 'int|min:0|exists:users,id',
            'notify' => 'boolean'
        ]);

        $userId = $user->id;
        $followedMod= FollowedUser::where('user_id', $userId)->where('follow_user_id', $val['user_id'])->first();

        if (isset($followedMod)) {
            $followedMod->update(['follow_user_id' => $val['user_id'], 'notify' => $val['notify']]);
        } else {
            FollowedUser::create(['follow_user_id' => $val['user_id'], 'user_id' => $userId, 'notify' => $val['notify']]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id, Authenticatable $user)
    {
        FollowedUser::where('user_id', $user->id)->where('follow_user_id', $id)->delete();
    }
}
