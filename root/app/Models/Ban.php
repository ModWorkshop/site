<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Log;

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
 * @property int|null $case_id
 * @property-read \App\Models\UserCase|null $case
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereCaseId($value)
 * @property int|null $game_id
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereGameId($value)
 * @property bool $can_appeal
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereCanAppeal($value)
 * @property int|null $mod_user_id
 * @property bool $active
 * @property-read \App\Models\User|null $modUser
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereModUserId($value)
 * @mixin \Eloquent
 */
class Ban extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected $with = ['modUser'];

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

    public function modUser()
    {
        return $this->belongsTo(User::class)->without('ban');
    }
    
    public function deactivate()
    {
        $this->update([
            'active' => false
        ]);
    }
}
