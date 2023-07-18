<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\FollowedGame
 *
 * @property int $id
 * @property int $user_id
 * @property int $game_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|FollowedGame newModelQuery()
 * @method static Builder|FollowedGame newQuery()
 * @method static Builder|FollowedGame query()
 * @method static Builder|FollowedGame whereCreatedAt($value)
 * @method static Builder|FollowedGame whereGameId($value)
 * @method static Builder|FollowedGame whereId($value)
 * @method static Builder|FollowedGame whereUpdatedAt($value)
 * @method static Builder|FollowedGame whereUserId($value)
 * @property-read User $user
 * @mixin Eloquent
 */
class FollowedGame extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMorphClass(): string {
        return 'followed_game';
    }
}
