<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
 */
class Thread extends Model
{
    use HasFactory, Filterable;

    protected $with = ['user', 'lastUser', 'category'];

    protected $guarded = [];
    
    protected $casts = [
        'bumped_at' => 'datetime',
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

    public function category()
    {
        return $this->belongsTo(ForumCategory::class);
    }
    
    public function comments() : MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('reply_to');
    }

    protected static function booted() {
        static::deleting(function (Thread $thread)
        {
            foreach ($thread->comments as $comment) {
                $comment->delete();
            }
        });
    }
}
