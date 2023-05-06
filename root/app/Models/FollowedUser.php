<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\FollowedUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $follow_user_id
 * @property bool $notify
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser whereFollowUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser whereNotify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser whereUserId($value)
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 */
class FollowedUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
