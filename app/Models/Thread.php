<?php

namespace App\Models;

use App\Interfaces\SubscribableInterface;
use App\Traits\Reportable;
use App\Traits\Subscribable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Models\Thread
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Thread newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Thread newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Thread query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $content
 * @property int $views
 * @property-read int|null $comments_count
 * @property bool $archived
 * @property string $bumped_at
 * @property string $pinned_at
 * @property int $forum_id
 * @property int|null $category_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereBumpedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereForumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread wherePinnedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereViews($value)
 * @property int $last_user_id
 * @property-read \App\Models\Forum $forum
 * @property-read \App\Models\User $lastUser
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereLastUserId($value)
 * @property-read \App\Models\ForumCategory|null $category
 * @property bool $archived_by_mod
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereArchivedByMod($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \App\Models\Subscription|null $subscribed
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Taggable[] $tagsSpecial
 * @property-read int|null $tags_special_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Report[] $reports
 * @property-read int|null $reports_count
 * @property bool $locked
 * @property bool $locked_by_mod
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereLockedByMod($value)
 * @property bool $announce
 * @property \Illuminate\Support\Carbon|null $announce_until
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereAnnounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereAnnounceUntil($value)
 */
class Thread extends Model implements SubscribableInterface
{
    use HasFactory, Subscribable, Reportable;

    protected $with = ['user', 'lastUser', 'category'];

    public $commentsOrder = 'ASC';

    protected $guarded = [];
    
    protected $casts = [
        'bumped_at' => 'datetime',
        'announce_until' => 'datetime',
    ];

    public function getMorphClass(): string {
        return 'thread';
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function lastUser() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function forum() : BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    public function game() : BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function category()
    {
        return $this->belongsTo(ForumCategory::class);
    }
    
    public function comments() : MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('reply_to');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function tagsSpecial(): HasMany
    {
        return $this->hasMany(Taggable::class, 'taggable_id')->where('taggable_type', 'thread');
    }

    protected static function booted() {
        static::created(function(Thread $thread) {
            $thread->game_id = $thread->forum->game_id;
            $thread->save();
        });

        static::deleting(function (Thread $thread)
        {
            foreach ($thread->comments as $comment) {
                $comment->delete();
            }

            $thread->subscriptions()->delete();
        });
    }
}
