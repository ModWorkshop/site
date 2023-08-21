<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\BlockedUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

/**
 * @group Blocked Users
 * 
 * @authenticated
 */
class BlockedUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get List of Blocked Users
     */
    public function index(FilteredRequest $request)
    {
        return JsonResource::collection($this->user()->blockedUsers()->queryGet($request->val()));
    }

    /**
     * Create Blocked User
     */
    public function store(Request $request)
    {
        $user = $this->user();

        $val = $request->validate([
            'user_id' => "int|min:0|exists:users,id|not_in:{$user->id}",
            'silent' => 'boolean'
        ]);

        $blocked = $user->blockedUsers()->wherePivot('block_user_id', $val['user_id'])->exists();
        if ($blocked) {
            $user->blockedUsers()->updateExistingPivot($val['user_id'], [
                'silent' => $val['silent']
            ]);
        } else {
            $user->blockedUsers()->attach($val['user_id'], [
                'silent' => $val['silent']
            ]);
        }
    }

    /**
     * Delete Blocked User
     */
    public function destroy(Request $request, $id)
    {
        $user = $this->user();
        $user->blockedUsers()->detach($id);
    }
}
