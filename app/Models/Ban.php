<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rennokki\QueryCache\Traits\QueryCacheable;

/**
 * App\Models\Ban
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $reason
 * @property \Illuminate\Support\Carbon $expire_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Ban newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereUserId($value)
 * @mixin \Eloquent
 * @property int|null $case_id
 * @property-read \App\Models\UserCase|null $case
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereCaseId($value)
 * @property int|null $game_id
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereGameId($value)
 */
class Ban extends Model
{
    use HasFactory, QueryCacheable;

    public $cacheFor = 1;
    public static $flushCacheOnUpdate = true;

    protected $guarded = [];
    
    protected $with = ['case'];

    protected $casts = [
        'expire_date' => 'datetime',
    ];

    public function getMorphClass(): string {
        return 'ban';
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class)->without('ban');
    }

    public function case() : BelongsTo
    {
        return $this->belongsTo(UserCase::class, 'case_id')->without(['user', 'ban']);
    }
}
