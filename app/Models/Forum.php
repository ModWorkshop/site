<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Forum
 *
 * @property int $id
 * @property int|null $game_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Forum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum query()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Game|null $game
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Thread[] $threads
 * @property-read int|null $threads_count
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ForumCategory[] $categories
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereName($value)
 */
class Forum extends Model
{
    use HasFactory, Filterable;

    protected $guarded = [];

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
