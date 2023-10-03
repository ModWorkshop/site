<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

/**
 * App\Models\Forum
 *
 * @property int $id
 * @property int|null $game_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Forum newModelQuery()
 * @method static Builder|Forum newQuery()
 * @method static Builder|Forum query()
 * @method static Builder|Forum whereCreatedAt($value)
 * @method static Builder|Forum whereGameId($value)
 * @method static Builder|Forum whereId($value)
 * @method static Builder|Forum whereUpdatedAt($value)
 * @property-read Game|null $game
 * @property-read Collection|Thread[] $threads
 * @property-read int|null $threads_count
 * @property string $name
 * @property-read Collection|ForumCategory[] $categories
 * @property-read int|null $categories_count
 * @method static Builder|Forum whereName($value)
 * @mixin Eloquent
 */
class Forum extends Model implements Sitemapable
{
    use HasFactory;

    protected $guarded = [];

    public function toSitemapTag(): Url | string | array
    {
        return Url::create(env('FRONTEND_URL').(isset($this->game) ? '/g/'.$this->game->short_name.'/forum' : '/forum'))
            ->setLastModificationDate(\Carbon\Carbon::create($this->last_date))
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(0.8);
    }

    public function getMorphClass(): string {
        return 'forum';
    }

    public function game() : BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function threads() : HasMany
    {
        return $this->hasMany(Thread::class);
    }

    public function categories() : HasMany
    {
        return $this->hasMany(ForumCategory::class);
    }
}
