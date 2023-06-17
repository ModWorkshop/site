<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Supporter;
use App\Services\Utils;
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
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val([
            'active_only' => 'boolean',
            'sort_by_id' => 'boolean',
        ]);

        $q = Supporter::query();

        if (isset($val['sort_by_id']) && $val['sort_by_id']) {
            $q->groupBy('id');
        } else {
            $q->orderByRaw('expire_date DESC NULLS LAST, expired ASC');
        }
        if (isset($val['active_only']) && $val['active_only']) {
            $q->whereNull('expire_date')->orWhereDate('expire_date', '>', Carbon::now());
        }

        return JsonResource::collection($q->get()->unique('user_id')->flatten()->paginate(1000));
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

        Utils::convertToUTC($val, 'expire_date');

        if (Supporter::where('user_id', $val['user_id'])->where(fn($q) => $q->whereNull('expire_date')->orWhereDate('expire_date', '>', Carbon::now()))->exists()) {
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
            'expired' => 'boolean|nullable'
        ]);

        Utils::convertToUTC($val, 'expire_date');

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
