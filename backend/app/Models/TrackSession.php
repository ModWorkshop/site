<?php

namespace App\Models;

use Cache;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * App\Models\TrackSession
 *
 * @property int $id
 * @property string $ip_address
 * @property int|null $user_id
 * @property Carbon|null $updated_at
 * @method static Builder|TrackSession newModelQuery()
 * @method static Builder|TrackSession newQuery()
 * @method static Builder|TrackSession query()
 * @method static Builder|TrackSession whereId($value)
 * @method static Builder|TrackSession whereIpAddress($value)
 * @method static Builder|TrackSession whereUpdatedAt($value)
 * @method static Builder|TrackSession whereUserId($value)
 * @mixin Eloquent
 */
class TrackSession extends Model
{
    public const CREATED_AT = null;

    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public static function userCount() {
        return Cache::remember('user-count', 60,
            fn() => TrackSession::whereNotNull('user_id')
                ->where('updated_at', '>', Carbon::now()->subMinutes(15))
                ->count()
        );
    }

    public static function guestCount() {
        return Cache::remember('guest-count', 60,
            fn() => TrackSession::whereNull('user_id')
                ->where('updated_at', '>', Carbon::now()->subMinutes(15))
                ->count()
        );
    }

    use HasFactory;
}
