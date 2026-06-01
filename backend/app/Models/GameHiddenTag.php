<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $game_id
 * @property int $tag_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Game $game
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game> $games
 * @property-read int|null $games_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameHiddenTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameHiddenTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameHiddenTag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameHiddenTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameHiddenTag whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameHiddenTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameHiddenTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameHiddenTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GameHiddenTag extends Model
{
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function games() {
        return $this->belongsToMany(Game::class);
    }

    public function getMorphClass(): string {
        return 'game_hidden_tag';
    }
}
