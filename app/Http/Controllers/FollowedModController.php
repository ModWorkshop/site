<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetModsRequest;
use App\Http\Resources\ModResource;
use App\Models\FollowedGame;
use App\Models\FollowedMod;
use App\Models\Mod;
use App\Models\User;
use App\Services\ModService;
use DB;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class FollowedModController extends Controller
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
    public function index(GetModsRequest $request, Authenticatable $user)
    {
        $val = $request->val();
        $mods = Mod::queryGet($val, function($query, $val) use ($user) {
            $query->whereExists(function($query) use ($user) {
                $query->from('followed_mods')->select(DB::raw(1))->where('user_id', $user->id);
                $query->whereColumn('followed_mods.mod_id', 'mods.id');
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
            'mod_id' => 'int|min:0|exists:mods,id',
            'notify' => 'boolean'
        ]);

        $userId = $user->id;
        $followedMod= FollowedMod::where('user_id', $userId)->where('mod_id', $val['mod_id'])->first();

        if (isset($followedMod)) {
            $followedMod->update(['mod_id' => $val['mod_id'], 'notify' => $val['notify']]);
        } else {
            FollowedMod::create(['mod_id' => $val['mod_id'], 'user_id' => $userId, 'notify' => $val['notify']]);
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
        FollowedMod::where('user_id', $user->id)->where('mod_id', $id)->delete();
        User::flushQueryCache();
    }
}
