<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Ban;
use App\Models\User;
use App\Models\UserCase;
use Arr;
use Carbon\Carbon;
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
            $query->where(fn($q) => $q->where('expire_date', '>', Carbon::now())->orWhereNull('expire_date'));
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
            'expire_date' => 'date|after:now|nullable',
            'reason' => 'string|min:3|max:1000'
        ]);

        $user = $request->user();
        /**
         * @var User
         */
        $banUser = User::find($val['user_id']);
        
        if ($banUser->ban) {
            abort(405, 'Already banned'); //Already banned
        }

        if (!$banUser->canBeEdited($user)) {
            abort(403, 'Cannot ban user');
        }

        $reason = Arr::pull($val, 'reason');

        $case = UserCase::create([
            'warning' => false,
            'reason' => $reason,
            'user_id' => $val['user_id'],
            'expire_date' => $val['expire_date']
        ]);

        $val['case_id'] = $case->id;

        $ban = Ban::create($val);

        return $ban->load(['user', 'case']);
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
