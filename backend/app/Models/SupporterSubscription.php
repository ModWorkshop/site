<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $supporter_package_id
 * @property string $status
 * @property float $price
 * @property string $provider
 * @property string $provider_id
 * @property string|null $next_payment_at
 * @property string|null $cancelled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Supporter|null $supporter
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription whereNextPaymentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription whereSupporterPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterSubscription whereUserId($value)
 * @mixin \Eloquent
 */
class SupporterSubscription extends Model
{
    protected $guarded = [];

    public function supporter() {
        return $this->belongsTo(Supporter::class);
    }

    public function user() {
        return $this->hasOne(User::class);
    }
}
