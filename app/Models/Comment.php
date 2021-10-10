<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    use HasFactory;

    protected $with = 'user';
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

    public function replyingComment() : BelongsTo
    {
        return $this->belongsTo(Comment::class, 'id', 'reply_to');
    }
}
