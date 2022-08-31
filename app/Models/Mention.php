<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Mention
 *
 * @property int $id
 * @property int $user_id
 * @property int $comment_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Mention newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mention newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mention query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mention whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mention whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mention whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mention whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mention whereUserId($value)
 * @mixin \Eloquent
 */
class Mention extends Model
{
    use HasFactory;

    
    public function getMorphClass(): string {
        return 'mention';
    }
}
