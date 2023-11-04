<?php

namespace App\Models;

use App\Interfaces\SubscribableInterface;
use App\Traits\Reportable;
use App\Traits\Subscribable;
use Auth;
use Eloquent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Laravel\Scout\Attributes\SearchUsingFullText;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $commentable
 * @property-read Collection|Comment[] $lastReplies
 * @property-read int|null $last_replies_count
 * @property-read Comment $replyingComment
 * @property-read User $user
 * @method static Builder|Comment newModelQuery()
 * @method static Builder|Comment newQuery()
 * @method static Builder|Comment query()
 * @method static Builder|Comment whereCommentableId($value)
 * @method static Builder|Comment whereCommentableType($value)
 * @method static Builder|Comment whereContent($value)
 * @method static Builder|Comment whereCreatedAt($value)
 * @method static Builder|Comment whereId($value)
 * @method static Builder|Comment wherePinned($value)
 * @method static Builder|Comment whereReplyTo($value)
 * @method static Builder|Comment whereUpdatedAt($value)
 * @method static Builder|Comment whereUserId($value)
 * @property-read Collection|User[] $mentions
 * @property-read int|null $mentions_count
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection|Comment[] $replies
 * @property-read int|null $replies_count
 * @property-read Subscription|null $subscribed
 * @property-read Collection|Report[] $reports
 * @property-read int|null $reports_count
 * @mixin Eloquent
 */
class Comment extends Model implements SubscribableInterface
{
    use HasFactory, Subscribable, Reportable, HasEagerLimit;

    protected $with = ['user', 'mentions', 'subscribed'];
    protected $guarded = [];
    protected $hidden = [];
    protected $saveToReport = ['content'];

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

    public function replies()
    {
        return $this->hasMany(Comment::class, 'reply_to', 'id')->orderBy('id');
    }

    public function lastReplies()
    {
        return $this->hasMany(Comment::class, 'reply_to', 'id')->orderBy('id')->limit(3);
    }

    public function replyingComment() : BelongsTo
    {
        return $this->belongsTo(Comment::class, 'reply_to', 'id');
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
        static::created(function(Comment $comment) {
            $id = Auth::user()?->id;
            if (isset($id)) {
                if (isset($comment->reply_to)) {
                    $subs = $comment->replyingComment->subscriptions();
                    if (isset($subs) && !$subs->where('user_id', $id)->exists()) {
                        $comment->replyingComment->subscriptions()->create([
                            'user_id' => $id
                        ]);
                    }
                } else {
                    $comment->subscriptions()->create([
                        'user_id' => $id
                    ]);
                }
            }
        });

        static::deleted(function(Comment $comment) {
            Notification::deleteRelated($comment);
            $comment->subscriptions()->delete();
            if (isset($comment->commentable->comment_count)) {
                $comment->commentable->decrement('comment_count');
            }

            if (isset($comment->commentable) && method_exists($comment->commentable, 'onCommentDeleted')) {
                $comment->commentable->onCommentDeleted($comment);
            }
        });
    }
}
