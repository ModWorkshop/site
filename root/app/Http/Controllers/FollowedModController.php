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
use Illuminate\Http\Response;

/**
 * @group Users
 *
 * @subgroup Followed Mods
 *
 * @authenticated
 */
class FollowedModController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get List of Followed Mods
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
        return ModResource::collectionResponse($mods);
    }

    /**
     * Add Followed Mod
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
     * @hideFromApiDocumentation
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Delete Followed Mod
     */
    public function destroy(int $id, Authenticatable $user)
    {
        FollowedMod::where('user_id', $user->id)->where('mod_id', $id)->delete();
    }
}
