<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\Tag
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property string $notice
 * @property int $notice_type
 * @property bool $notice_localized
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static TagFactory factory(...$parameters)
 * @method static Builder|Tag newModelQuery()
 * @method static Builder|Tag newQuery()
 * @method static Builder|Tag query()
 * @method static Builder|Tag whereColor($value)
 * @method static Builder|Tag whereCreatedAt($value)
 * @method static Builder|Tag whereId($value)
 * @method static Builder|Tag whereName($value)
 * @method static Builder|Tag whereNotice($value)
 * @method static Builder|Tag whereNoticeLocalized($value)
 * @method static Builder|Tag whereNoticeType($value)
 * @method static Builder|Tag whereUpdatedAt($value)
 * @property-read Collection|Category[] $categories
 * @property-read int|null $categories_count
 * @property-read Collection|Game[] $games
 * @property-read int|null $games_count
 * @property int|null $game_id
 * @property string|null $only_for
 * @property-read Game|null $game
 * @property-read Collection|Mod[] $mods
 * @property-read int|null $mod_count
 * @property-read Collection|Thread[] $threads
 * @property-read int|null $threads_count
 * @method static Builder|Tag whereGameId($value)
 * @method static Builder|Tag whereOnlyFor($value)
 * @property string|null $type
 * @method static Builder|Tag whereType($value)
 * @property-read int|null $mods_count
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag all($columns = [])
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag avg($column)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag cache(array $tags = [])
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag cachedValue(array $arguments, string $cacheKey)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag count($columns = '*')
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag disableCache()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag disableModelCaching()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag exists()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag flushCache(array $tags = [])
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag getModelCacheCooldown(\Illuminate\Database\Eloquent\Model $instance)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag inRandomOrder($seed = '')
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag insert(array $values)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag max($column)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag min($column)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag sum($column)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag truncate()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Tag withCacheCooldownSeconds(?int $seconds = null)
 * @mixin Eloquent
 */
class Tag extends Model
{
    use HasFactory;

    protected $hidden = ['pivot'];

    protected $guarded = [];

    public function getMorphClass(): string {
        return 'tag';
    }

    public function game() {
        return $this->belongsTo(Game::class);
    }

    public function mods() {
        return $this->morphedByMany(Mod::class, 'taggable');
    }

    public function threads() {
        return $this->morphedByMany(Thread::class, 'taggable');
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }
}
