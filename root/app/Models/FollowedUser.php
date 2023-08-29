<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\FollowedUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $follow_user_id
 * @property bool $notify
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|FollowedUser newModelQuery()
 * @method static Builder|FollowedUser newQuery()
 * @method static Builder|FollowedUser query()
 * @method static Builder|FollowedUser whereCreatedAt($value)
 * @method static Builder|FollowedUser whereFollowUserId($value)
 * @method static Builder|FollowedUser whereId($value)
 * @method static Builder|FollowedUser whereNotify($value)
 * @method static Builder|FollowedUser whereUpdatedAt($value)
 * @method static Builder|FollowedUser whereUserId($value)
 * @property-read User $user
 * @mixin Eloquent
 */
class FollowedUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMorphClass(): string {
        return 'followed_user';
    }
}
