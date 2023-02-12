<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\UserCase
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $mod_user_id
 * @property bool $warning
 * @property string $reason
 * @property string|null $expire_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereModUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereWarning($value)
 * @mixin \Eloquent
 * @property string|null $pardon_reason
 * @property bool $pardoned
 * @property-read \App\Models\Ban|null $ban
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase wherePardonReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase wherePardoned($value)
 * @property int|null $game_id
 * @property-read \App\Models\User|null $modUser
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereGameId($value)
 */
class UserCase extends Model
{
    protected $guarded = [];
    protected $with = ['user', 'ban'];
    protected $casts = [
        'expire_date' => 'datetime',
        'pardoned' => 'boolean'
    ];

    use HasFactory;

    public function getMorphClass(): string {
        return 'user_case';
    }

    public function ban(): HasOne
    {
        return $this->hasOne(Ban::class, 'case_id')->without('case');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->without('ban');
    }

    public function modUser()
    {
        return $this->belongsTo(User::class)->without('ban');
    }

    protected static function booted()
    {
        self::deleting(function(UserCase $userCase) {
            Notification::deleteRelated($userCase);
        });
    }
}
