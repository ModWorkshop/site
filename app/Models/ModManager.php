<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $download_url
 * @property string $site_url
 * @property int|null $game_id
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Game|null $game
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game> $games
 * @property-read int|null $games_count
 * @method static \Illuminate\Database\Eloquent\Builder|ModManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModManager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModManager whereDownloadUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModManager whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModManager whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModManager whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModManager whereSiteUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModManager whereUpdatedAt($value)
 * @property bool $hidden
 * @method static \Illuminate\Database\Eloquent\Builder|ModManager whereHidden($value)
 * @mixin \Eloquent
 */
class ModManager extends Model
{
    protected $guarded = [];

    use HasFactory;

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function games() {
        return $this->belongsToMany(Game::class);
    }
}
