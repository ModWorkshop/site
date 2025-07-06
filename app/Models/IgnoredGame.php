<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $game_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredGame newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredGame query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredGame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredGame whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredGame whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredGame whereUserId($value)
 * @mixin \Eloquent
 */
class IgnoredGame extends Model
{
    protected $guarded = [];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMorphClass(): string {
        return 'ignored_game';
    }
}
