<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\UserCase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCaseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(UserCase::class, 'user_Case');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $val = $request->validate([
            'user_id' => 'int|min:0|nullable|exists:users,id'
        ]);
        return JsonResource::collection(UserCase::queryGet($val, function($query, $val) {
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
            'user_id' => 'int|min:0|nullable|exists:users,id',
            'reason' => 'string|min:3|max:1000',
            'expire_date' => 'date|required|after:now'
        ]);

        $val['mod_user_id'] = $this->userId();

        UserCase::create($val);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(UserCase $userCase)
    {
        return $userCase;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserCase $userCase)
    {
        $val = $request->validate([
            'reason' => 'string|min:3|max:1000',
            'expire_date' => 'date|required|after:now'
        ]);

        $userCase->update($val);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserCase $userCase)
    {
        $userCase->delete();
    }
}
