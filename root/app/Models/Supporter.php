<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Supporter
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $expire_date
 * @property bool $expired
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|Supporter newModelQuery()
 * @method static Builder|Supporter newQuery()
 * @method static Builder|Supporter query()
 * @method static Builder|Supporter whereCreatedAt($value)
 * @method static Builder|Supporter whereExpireDate($value)
 * @method static Builder|Supporter whereId($value)
 * @method static Builder|Supporter whereIsCancelled($value)
 * @method static Builder|Supporter whereUpdatedAt($value)
 * @method static Builder|Supporter whereUserId($value)
 * @method static Builder|Supporter whereExpired($value)
 * @mixin Eloquent
 */
class Supporter extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['user'];

    protected $casts = [
        'expire_date' => 'datetime',
    ];

    public function getMorphClass(): string {
        return 'supporter';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->without('supporter');
    }
}
