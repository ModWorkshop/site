<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Log;

/**
 * App\Models\Ban
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $reason
 * @property Carbon $expire_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 * @method static Builder|Ban newModelQuery()
 * @method static Builder|Ban newQuery()
 * @method static Builder|Ban query()
 * @method static Builder|Ban whereCreatedAt($value)
 * @method static Builder|Ban whereExpireDate($value)
 * @method static Builder|Ban whereId($value)
 * @method static Builder|Ban whereReason($value)
 * @method static Builder|Ban whereUpdatedAt($value)
 * @method static Builder|Ban whereUserId($value)
 * @property int|null $case_id
 * @property-read UserCase|null $case
 * @method static Builder|Ban whereCaseId($value)
 * @property int|null $game_id
 * @method static Builder|Ban whereGameId($value)
 * @property bool $can_appeal
 * @method static Builder|Ban whereCanAppeal($value)
 * @property int|null $mod_user_id
 * @property bool $active
 * @property-read User|null $modUser
 * @method static Builder|Ban whereActive($value)
 * @method static Builder|Ban whereModUserId($value)
 * @property-read \App\Models\Game|null $game
 * @mixin Eloquent
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

    public function game() : BelongsTo
    {
        return $this->belongsTo(Game::class);
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
