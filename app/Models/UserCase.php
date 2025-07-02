<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserCase
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $mod_user_id
 * @property bool $warning
 * @property string $reason
 * @property string|null $expire_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|UserCase newModelQuery()
 * @method static Builder|UserCase newQuery()
 * @method static Builder|UserCase query()
 * @method static Builder|UserCase whereCreatedAt($value)
 * @method static Builder|UserCase whereExpireDate($value)
 * @method static Builder|UserCase whereId($value)
 * @method static Builder|UserCase whereModUserId($value)
 * @method static Builder|UserCase whereReason($value)
 * @method static Builder|UserCase whereUpdatedAt($value)
 * @method static Builder|UserCase whereUserId($value)
 * @method static Builder|UserCase whereWarning($value)
 * @property string|null $pardon_reason
 * @property bool $pardoned
 * @property-read Ban|null $ban
 * @property-read User $user
 * @method static Builder|UserCase wherePardonReason($value)
 * @method static Builder|UserCase wherePardoned($value)
 * @property int|null $game_id
 * @property-read User|null $modUser
 * @method static Builder|UserCase whereGameId($value)
 * @property bool $active
 * @method static Builder|UserCase whereActive($value)
 * @mixin Eloquent
 */
class UserCase extends Model
{
    protected $guarded = [];
    protected $with = ['user'];
    protected $casts = [
        'expire_date' => 'datetime',
    ];

    use HasFactory;

    public function getMorphClass(): string {
        return 'user_case';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->without('ban');
    }

    public function modUser()
    {
        return $this->belongsTo(User::class)->without('ban');
    }
    
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    protected static function booted()
    {
        static::deleting(function(UserCase $userCase) {
            Notification::deleteRelated($userCase);
        });
    }
}
