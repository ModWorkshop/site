<?php

namespace App\Models;

use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Chelout\RelationshipEvents\Traits\HasRelationshipObservables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

/**
 * App\Models\Comment
 *
 * @property int $id
 * @property string $commentable_type
 * @property int $commentable_id
 * @property int $user_id
 * @property string $content
 * @property bool $pinned
 * @property int|null $reply_to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $commentable
 * @property-read \Illuminate\Database\Eloquent\Collection|Comment[] $lastReplies
 * @property-read int|null $last_replies_count
 * @property-read Comment $replyingComment
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment wherePinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereReplyTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $mentions
 * @property-read int|null $mentions_count
 */
class Comment extends Model
{
    use HasFactory, QueryCacheable, HasBelongsToManyEvents, HasRelationshipObservables;

    public $cacheFor = 60;
    public static $flushCacheOnUpdate = true;

    protected $with = ['user'];
    protected $guarded = [];
    protected $hidden = [];

    public function getMorphClass(): string {
        return 'comment';
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentable() : MorphTo
    {
        return $this->morphTo();
    }

    public function lastReplies() : HasMany
    {
        return $this->hasMany(Comment::class, 'reply_to')->oldest()->limit(3);
    }

    public function replyingComment() : BelongsTo
    {
        return $this->belongsTo(Comment::class, 'id', 'reply_to');
    }

    public function mentions()
    {
        return $this->belongsToMany(User::class, Mention::class);
    }

    #[SearchUsingFullText(['content'])]
    public function toSearchableArray()
    {
        return [
            'content' => $this->content
        ];
    }

    public static function booted()
    {
        self::deleted(function(Comment $comment) {
            Notification::deleteRelated($comment);
        });
    }
}
