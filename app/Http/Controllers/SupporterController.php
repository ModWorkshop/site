<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Supporter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupporterController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Supporter::class, 'supporter');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request)
    {
        return JsonResource::collection(Supporter::queryGet($request->val()));
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
            'user_id' => 'int|min:1|required|exists:users,id',
            'expire_date' => 'date|after:now|nullable'
        ]);
        
        if (Supporter::where('user_id', $val['user_id'])->whereNull('expire_date')->orWhereDate('expire_date', '>', Carbon::now())->exists()) {
            abort(409, 'Supporter membership already exists!');
        }

        return Supporter::create($val)->load('user');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Supporter $supporter)
    {
        return $supporter;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supporter $supporter)
    {
        $val = $request->validate([
            'expire_date' => 'date|nullable',
            'is_cancelled' => 'in:true,false|nullable'
        ]);

        $supporter->update($val);

        return $supporter;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supporter $supporter)
    {
        $supporter->delete();
    }
}
