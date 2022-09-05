<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\User;
use Illuminate\Http\Request;

class BlockedUserController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * @var User
         */
        $user = $request->user();

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
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $user->blockedUsers()->detach($id);
    }
}
