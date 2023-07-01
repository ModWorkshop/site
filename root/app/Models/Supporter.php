<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Supporter
 *
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $expire_date
 * @property bool $expired
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter query()
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter whereIsCancelled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter whereExpired($value)
 * @mixin \Eloquent
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
