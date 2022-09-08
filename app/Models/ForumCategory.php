<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\ForumCategory
 *
 * @property int $id
 * @property string $name
 * @property string $desc
 * @property int $forum_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereForumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $emoji
 * @property-read \App\Models\Forum $forum
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereEmoji($value)
 */
class ForumCategory extends Model
{
    use HasFactory;

    public function getMorphClass(): string {
        return 'forum_category';
    }

    protected $guarded = [];

    public function forum() : BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }
}
