<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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
class Forum extends Model
{
    use HasFactory;

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

    public static function booted() {
        static::created(function(Model $forum) {
            $forum->createDefaultCategories();
        });
    }

    public function createDefaultCategories() {
        if (empty($this->game_id)) {
            return;
        }

        $cats = $this->categories();

        $this->withSecureConstraints(fn() => $this->categories()->firstOrCreate([
            'name' => 'General',
        ], [
            'display_order' => 100,
            'emoji' => '💬',
            'desc' => 'A forum to discuss general things about the game.'
        ]));

        if (!$cats->where('name', 'News')->exists()) {
            $news = $this->withSecureConstraints(fn() => $this->categories()->create([
                'name' => 'News',
                'display_order' => 90,
                'grid_mode' => true,
                'emoji' => '📰',
            ]));
            $news->roles()->attach(Role::find(1), ['can_post' => false, 'can_view' => true]);
        }

        $this->withSecureConstraints(fn() => $this->categories()->firstOrCreate([
            'name' => 'Modding Help',
        ], [
            'display_order' => 80,
            'emoji' => '🙏',
            'desc' => 'A forum to get help in modding the game.',
            'can_close_threads' => true
        ]));

        $this->withSecureConstraints(fn() => $this->categories()->firstOrCreate([
            'name' => 'Modding Requests',
        ],[
            'display_order' => 70,
            'emoji' => '❔',
            'desc' => 'A forum to create requests for new mods.',
            'can_close_threads' => true
        ]));
    }
}
