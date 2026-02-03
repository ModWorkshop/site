<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Supporter;
use App\Services\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Models\Subscription;
use App\Services\APIService;
use Illuminate\Http\Response;
use Tebex\Webhook\Webhook;
use Tebex\Webhooks;

/**
 * @group Supporters
 */
class SupporterController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Supporter::class, 'supporter');
    }
    /**
     * List supporters
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
            $q->where('expired', false);
            $q->where(fn($q) => $q->whereNull('expire_date')->orWhereDate('expire_date', '>', Carbon::now()));
        }

        return BaseResource::collectionResponse($q->get()->unique('user_id')->flatten()->paginate(1000));
    }

    /**
     * Create a supporter
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
     * Get a supporter
     */
    public function show(Supporter $supporter)
    {
        return $supporter;
    }

    /**
     * Update a supporter
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
     * Delete a supporter
     *
     * @authenticated
     */
    public function destroy(Supporter $supporter)
    {
        $supporter->delete();
    }


    public function tebexWebhook() {
        Webhooks::setSecretKey(env('TEBEX_SECRET_KEY'));
        $webhook = Webhook::parse();

        // Respond to validation endpoint
        if ($webhook->isType(\Tebex\Webhook\VALIDATION_WEBHOOK)) {
            return ["id" => $webhook->getId()];
        }
    }
    /**
     * Checks nitro to update the subscription data
     * 
     * @authenticated
     * 
     * @hideFromApiDocumentation
     */
    public function nitroCheck() {
        return APIService::nitroCheck($this->user());
    }
}
