<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\Mention
 *
 * @property int $id
 * @property int $user_id
 * @property int $comment_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Mention newModelQuery()
 * @method static Builder|Mention newQuery()
 * @method static Builder|Mention query()
 * @method static Builder|Mention whereCommentId($value)
 * @method static Builder|Mention whereCreatedAt($value)
 * @method static Builder|Mention whereId($value)
 * @method static Builder|Mention whereUpdatedAt($value)
 * @method static Builder|Mention whereUserId($value)
 * @mixin Eloquent
 */
class Mention extends Model
{
    use HasFactory;


    public function getMorphClass(): string {
        return 'mention';
    }
}
