<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetModsRequest;
use App\Http\Resources\ModResource;
use App\Models\FollowedGame;
use App\Models\FollowedMod;
use App\Models\Mod;
use App\Models\User;
use App\Services\ModService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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
     * List followed mods
     */
    public function index(GetModsRequest $request, Authenticatable $user)
    {
        $mods = ModService::mods($request->val(), function($query) use ($user) {
            $query->whereExists(function($query) use ($user) {
                $query->from('followed_mods')->select(DB::raw(1))->where('user_id', $user->id);
                $query->whereColumn('followed_mods.mod_id', 'mods.id');
            });
        });
        return ModResource::collectionResponse($mods);
    }

    /**
     * Update a followed mod
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
     * Delete a followed mod
     */
    public function destroy(int $id, Authenticatable $user)
    {
        FollowedMod::where('user_id', $user->id)->where('mod_id', $id)->delete();
    }
}
