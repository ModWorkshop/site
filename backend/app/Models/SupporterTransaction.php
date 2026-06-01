<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $supporter_package_id
 * @property int|null $supporter_subscription_id
 * @property string $status
 * @property float $price
 * @property string $provider
 * @property string $provider_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Supporter|null $supporter
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterTransaction wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterTransaction whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterTransaction whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterTransaction whereSupporterPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterTransaction whereSupporterSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupporterTransaction whereUserId($value)
 * @mixin \Eloquent
 */
class SupporterTransaction extends Model
{
    protected $guarded = [];

    public function supporter() {
        return $this->belongsTo(Supporter::class);
    }

    public function user() {
        return $this->hasOne(User::class);
    }
}
