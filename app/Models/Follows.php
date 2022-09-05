<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Follows
 *
 * @property int $id
 * @property int $user_id
 * @property string $followable_type
 * @property int $followable_id
 * @property bool $following
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Follows newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Follows newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Follows query()
 * @method static \Illuminate\Database\Eloquent\Builder|Follows whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Follows whereFollowableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Follows whereFollowableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Follows whereFollowing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Follows whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Follows whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Follows whereUserId($value)
 * @mixin \Eloquent
 */
class Follows extends Model
{
    use HasFactory;
}
