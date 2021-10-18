<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Comment extends Model
{
    use HasFactory;
    use HasEagerLimit;

    protected $with = ['user', 'lastReplies'];
    protected $guarded = [];
    protected $hidden = ['commentable', 'commentable_id', 'commentable_type'];

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
}
