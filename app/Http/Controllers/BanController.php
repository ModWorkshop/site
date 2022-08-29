<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Ban;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->validate([
            'user_id' => 'int|min:0|nullable|exists:users,id',
            'limit' => 'integer|min:1|max:1000',
        ]);
        
        return JsonResource::collection(Ban::queryGet($val, function($query) {
            $query->with('user');
            if (isset($val['user_id'])) {
                $query->where('user_id', $val['user_id']);
            }
        }));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $val = $request->validate([
            'user_id' => 'int|min:0|required|exists:users,id',
            'expire_date' => 'date|required|after:now',
            'reason' => 'string|min:3|max:1000'
        ]);

        $user = $request->user();
        /**
         * @var User
         */
        $banUser = User::find($val['user_id']);
        
        if (!$banUser->lastBan) {
            abort(405); //Already banned
        }

        if (!$banUser->canBeEdited($user)) {
            abort(403);
        }

        Ban::create($val);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Ban $ban)
    {
        return $ban;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ban $ban)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ban $ban)
    {
        $ban->delete();
    }
}
