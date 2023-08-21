<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Supporter;
use App\Services\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

/**
 * @group Supporters
 */
class SupporterController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Supporter::class, 'supporter');
    }
    /**
     * Get List of Supporters
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
     * Create Supporter
     * 
     * @authenticated
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
     * Show Supporter
     */
    public function show(Supporter $supporter)
    {
        return $supporter;
    }

    /**
     * Edit Supporter
     *
     * @authenticated
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
     * Delete Supporter
     *
     * @authenticated
     */
    public function destroy(Supporter $supporter)
    {
        $supporter->delete();
    }
}
