<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Supporter;
use App\Services\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Models\SupporterPackage;
use App\Models\SupporterSubscription;
use App\Models\SupporterTransaction;
use App\Models\User;
use App\Services\APIService;

const PAYMENT_STATUS = [
    1 => 'complete',
    2 => 'refunded'
];
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

    // public function getTebexProject() {
    //     return Headless::setProject(env('TEBEX_PUBLIC_KEY'));
    // }

    // public function createTebexBasketWithPackage(Request $request) {
    //     $val = $request->validate([
    //         'supporter_package_id' => "int|required|exists:supporter_packages,id"
    //     ]);

    //     Checkout::setApiKeys(env('TEBEX_PROJECT_ID'), env('TEBEX_PRIVATE_KEY'));

    //     $userId = $this->userId();
    //     $package = SupporterPackage::find($val['supporter_package_id']);

    //     $project = $this->getTebexProject();
    //     $basket = $project->createBasket(env('TEBEX_RETURN_URL'), env('TEBEX_RETURN_URL'));

    //     $basket->addPackage($project->getPackage($package->package_id));
    //     $basket = $basket->getBasket();
    //     $ident = $basket->getIdent();

    //     SupporterSubscription::create([ // Save the basket in the DB so we know who initiated this basket and who to give it to when webhook sends purchase complete
    //         'user_id' => $userId,
    //         'supporter_package_id' => $package->id,
    //         'price' => $package->price,
    //         'status' => 'waiting',
    //         'provider' => 'tebex',
    //         'provider_id' => 'tbx-r-'.$basket->getId()
    //     ]);

    //     return ['ident' => $ident];
    // }

    // public function tebexWebhook() {
    //     // Issue: Tebex uses REMOTE_ADDR to ensure the IP is not tempered with
    //     // This however isn't an issue on our end as we use a Proxy and replace that header with Caddy
    //     if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    //         $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
    //     }

    //     Webhooks::setSecretKey(env('TEBEX_WEBHOOK_SECRET_KEY'));

    //     $json = file_get_contents('php://input');
    //     $webhook = Webhook::parse($json);

    //     // Respond to validation endpoint
    //     if ($webhook->isType(\Tebex\Webhook\VALIDATION_WEBHOOK)) {
    //         return ["id" => $webhook->getId()];
    //     } else if ($webhook->isTypeOfPayment()) {
    //         /**
    //          * @var PaymentSubject
    //         */
    //         $subject = $webhook->getSubject();
    //         $prod = $subject->getProducts()[0];
    //         $ref = $subject->getRecurringPaymentReference();
    //         $sub = SupporterSubscription::find($ref);

    //         assert(isset($sub), "Reference {$ref} does not exist on site. Fix manually.");

    //         $user = User::find($sub->user_id);
    //         $expiryDate = Carbon::parse($prod->getExpiresAt());

    //         $transId = $subject->getTransactionId();
    //         $trans = SupporterTransaction::where('provider', 'tebex')->where('provider_id', $transId);
    //         if (!isset($trans)) {
    //             $trans = SupporterTransaction::create([
    //                 'user_id' => $user->id,
    //                 'supporter_package_id' => $sub->supporter_package_id,
    //                 'supporter_subcription_id' => $sub->id,
    //                 'price' => $sub->price,
    //                 'status' => 'complete',
    //                 'provider' => 'tebex',
    //                 'provider_id' => $subject->getTransactionId()
    //             ]);
    //         }
    //         $trans->update([
    //             'status' => PAYMENT_STATUS[$subject->getStatus()] ?? 'failed'
    //         ]);

    //         switch ($webhook->getType()) {
    //             case PAYMENT_COMPLETED:
    //                 Supporter::create([
    //                     'supporter_transaction_id' => $trans->id,
    //                     'user_id' => $user->id,
    //                     'expire_date' => Carbon::create($expiryDate),
    //                     'expired' => false
    //                 ]);
    //                 break;
    //             case PAYMENT_DECLINED:
    //             case PAYMENT_REFUNDED:
    //                 if (isset($trans->supporter)) {
    //                     $trans->supporter->expired = true;
    //                     $trans->supporter->save();
    //                 }
    //                 break;
    //         }
    //     } else if ($webhook->isTypeOfRecurringPayment()) {
    //         /**
    //          * @var RecurringPaymentSubject
    //         */
    //         $subject = $webhook->getSubject();
    //         $ref = $subject->getReference();
    //         $sub = SupporterSubscription::find($ref);

    //         assert(isset($sub), "Reference {$ref} does not exist on site. Fix manually.");

    //         switch ($webhook->getType()) {
    //             case RECURRING_PAYMENT_STARTED:
    //             case RECURRING_PAYMENT_RENEWED:
    //             case RECURRING_PAYMENT_CANCELLATION_ABORTED:
    //                 $sub->update(['status' => 'active', 'next_payment_at' => $subject->getNextPaymentAt()]);
    //                 break;
    //             case RECURRING_PAYMENT_CANCELLATION_REQUESTED:
    //                 $sub->update(['status' => 'cancelled', 'next_payment_at' => null]);
    //                 break;
    //         }
    //     }
    // }
}
