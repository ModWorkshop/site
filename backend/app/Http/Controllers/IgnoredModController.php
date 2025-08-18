<?php

namespace App\Http\Controllers;

use App\Http\Resources\ModResource;
use App\Models\IgnoredMod;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

/**
 * @group Users
 *
 * @subgroup Ignored mods
 *
 * @authenticated
 */
class IgnoredModController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List ignored mods
     */
    public function index(Request $request)
    {
        $val = $request->validate([
            'limit' => 'integer|min:1|max:50'
        ]);

        return ModResource::collectionResponse($this->user()->ignoredMods()->queryGet($val));
    }

    /**
     * Create an ignored mod
     */
    public function store(Request $request, Authenticatable $user)
    {
        $val = $request->validate([
            'mod_id' => 'int|min:0|exists:mods,id',
        ]);

        $userId = $user->id;
        if (IgnoredMod::where('user_id', $userId)->where('mod_id', $val['mod_id'])->exists()) {
            abort(409, 'Already ignoring mod');
        }

        IgnoredMod::create(['mod_id' => $val['mod_id'], 'user_id' => $userId]);
    }

    /**
     * Delete an ignored mod from ignored mods
     */
    public function destroy(int $id)
    {
        $this->user()->ignoredMods()->detach($id);
    }
}
