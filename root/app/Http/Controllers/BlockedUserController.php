<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\BlockedUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class BlockedUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(FilteredRequest $request)
    {
        return JsonResource::collection($this->user()->blockedUsers()->queryGet($request->val()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $user = $this->user();
        $user->blockedUsers()->detach($id);
    }
}
