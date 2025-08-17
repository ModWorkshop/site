<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\BlockedUser;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
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
     * List blocked users
     */
    public function index(FilteredRequest $request)
    {
        return BaseResource::collectionResponse($this->user()->blockedUsers()->queryGet($request->val()));
    }

    /**
     * Create a blocked user
     */
    public function store(Request $request)
    {
        $user = $this->user();

        $val = $request->validate([
            'block_user_id' => "int|min:1|exists:users,id|not_in:{$user->id}|required",
            'silent' => 'boolean'
        ]);

        $blockUserId = $val['block_user_id'];

        $blocked = $user->blockedUsers()->wherePivot('block_user_id', $blockUserId)->exists();
        if ($blocked) {
            $user->blockedUsers()->updateExistingPivot($blockUserId, [
                'silent' => $val['silent']
            ]);
        } else {
            $user->blockedUsers()->attach($blockUserId, [
                'silent' => $val['silent']
            ]);
        }
    }

    /**
     * Delete a blocked user
     */
    public function destroy(Request $request, $id)
    {
        $user = $this->user();
        $user->blockedUsers()->detach($id);
    }
}
