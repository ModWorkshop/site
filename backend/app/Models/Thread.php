<?php

namespace App\Models;

use App\Interfaces\SubscribableInterface;
use App\Traits\Reportable;
use App\Traits\Subscribable;
use Eloquent;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

/**
 * App\Models\Thread
 *
 * @method static Builder|Thread newModelQuery()
 * @method static Builder|Thread newQuery()
 * @method static Builder|Thread query()
 * @property int $id
 * @property string $name
 * @property string $content
 * @property int $views
 * @property-read int|null $comment_count
 * @property bool $archived
 * @property string $bumped_at
 * @property string $pinned_at
 * @property int $forum_id
 * @property int|null $category_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Comment[] $comments
 * @method static Builder|Thread whereArchived($value)
 * @method static Builder|Thread whereBumpedAt($value)
 * @method static Builder|Thread whereCategoryId($value)
 * @method static Builder|Thread whereCommentsCount($value)
 * @method static Builder|Thread whereContent($value)
 * @method static Builder|Thread whereCreatedAt($value)
 * @method static Builder|Thread whereForumId($value)
 * @method static Builder|Thread whereId($value)
 * @method static Builder|Thread whereName($value)
 * @method static Builder|Thread wherePinnedAt($value)
 * @method static Builder|Thread whereUpdatedAt($value)
 * @method static Builder|Thread whereUserId($value)
 * @method static Builder|Thread whereViews($value)
 * @property int $last_user_id
 * @property-read Forum $forum
 * @property-read User $lastUser
 * @property-read User $user
 * @method static Builder|Thread whereLastUserId($value)
 * @property-read ForumCategory|null $category
 * @property bool $archived_by_mod
 * @method static Builder|Thread whereArchivedByMod($value)
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Subscription|null $subscribed
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read Collection|Taggable[] $tagsSpecial
 * @property-read int|null $tags_special_count
 * @property-read Collection|Report[] $reports
 * @property-read int|null $reports_count
 * @property bool $locked
 * @property bool $locked_by_mod
 * @method static Builder|Thread whereLocked($value)
 * @method static Builder|Thread whereLockedByMod($value)
 * @property bool $announce
 * @property Carbon|null $announce_until
 * @method static Builder|Thread whereAnnounce($value)
 * @method static Builder|Thread whereAnnounceUntil($value)
 * @property int|null $game_id
 * @property-read Game|null $game
 * @method static Builder|Thread whereCommentCount($value)
 * @method static Builder|Thread whereGameId($value)
 * @property-read int|null $comments_count
 * @property string|null $edited_at
 * @property int|null $answer_comment_id
 * @property-read \App\Models\Comment|null $answerComment
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread all($columns = [])
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread avg($column)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread cache(array $tags = [])
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread cachedValue(array $arguments, string $cacheKey)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread count($columns = '*')
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread disableCache()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread disableModelCaching()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread exists()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread flushCache(array $tags = [])
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread getModelCacheCooldown(\Illuminate\Database\Eloquent\Model $instance)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread inRandomOrder($seed = '')
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread insert(array $values)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread isCachable()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread max($column)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread min($column)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread sum($column)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread truncate()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread whereAnswerCommentId($value)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread whereEditedAt($value)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread withCacheCooldownSeconds(?int $seconds = null)
 * @property bool $closed
 * @property bool $closed_by_mod
 * @property string $thumbnail
 * @property int $parser_version
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread whereClosed($value)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread whereClosedByMod($value)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread whereParserVersion($value)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Thread whereThumbnail($value)
 * @mixin Eloquent
 */
class Thread extends Model implements SubscribableInterface
{
    use HasFactory, Subscribable, Reportable, Cachable;

    protected $with = ['user', 'lastUser', 'category', 'game', 'tags'];
    protected $saveToReport = ['content'];

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
        return $this->belongsTo(Game::class, 'forum_id', 'forum_id');
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
        return $this->morphToMany(Tag::class, 'taggable')->orderByRaw('taggables.created_at');;
    }

    public function tagsSpecial(): HasMany
    {
        return $this->hasMany(Taggable::class, 'taggable_id')->where('taggable_type', 'thread');
    }

    // Runs the moment a comment is deleted to handle removal of last user ID properly
    public function onCommentDeleted(Comment $comment)
    {
        $firstComment = $this->withSecureConstraints(fn() => $this->comments()->latest('id')->first());
        $this->update([
            'last_user_id' => $firstComment->user_id ?? $this->user_id,
            'bumped_at' => $firstComment->created_at ?? $this->created_at
        ]);
    }

    public function answerComment()
    {
        return $this->belongsTo(Comment::class);
    }
    protected static function booted() {
        static::created(function(Thread $thread) {
            if ($thread->user->extra->auto_subscribe_to_thread) {
                $thread->subscriptions()->create([
                    'user_id' => $thread->user_id
                ]);
            }
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
