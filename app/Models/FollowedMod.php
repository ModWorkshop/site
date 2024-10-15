<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\FollowedMod
 *
 * @property int $id
 * @property int $user_id
 * @property int $mod_id
 * @property bool $notify
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|FollowedMod newModelQuery()
 * @method static Builder|FollowedMod newQuery()
 * @method static Builder|FollowedMod query()
 * @method static Builder|FollowedMod whereCreatedAt($value)
 * @method static Builder|FollowedMod whereId($value)
 * @method static Builder|FollowedMod whereModId($value)
 * @method static Builder|FollowedMod whereNotify($value)
 * @method static Builder|FollowedMod whereUpdatedAt($value)
 * @method static Builder|FollowedMod whereUserId($value)
 * @property-read User $user
 * @mixin Eloquent
 */
class FollowedMod extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMorphClass(): string {
        return 'followed_mod';
    }
}
