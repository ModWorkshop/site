<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\BlockedUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $block_user_id
 * @property bool $silent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|BlockedUser newModelQuery()
 * @method static Builder|BlockedUser newQuery()
 * @method static Builder|BlockedUser query()
 * @method static Builder|BlockedUser whereBlockUserId($value)
 * @method static Builder|BlockedUser whereCreatedAt($value)
 * @method static Builder|BlockedUser whereId($value)
 * @method static Builder|BlockedUser whereSilent($value)
 * @method static Builder|BlockedUser whereUpdatedAt($value)
 * @method static Builder|BlockedUser whereUserId($value)
 * @mixin Eloquent
 */
class BlockedUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getMorphClass(): string {
        return 'blocked_user';
    }
}
