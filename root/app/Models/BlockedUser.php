<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BlockedUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $block_user_id
 * @property bool $silent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser whereBlockUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser whereSilent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser whereUserId($value)
 * @mixin \Eloquent
 */
class BlockedUser extends Model
{
    use HasFactory;

    public function getMorphClass(): string {
        return 'blocked_user';
    }
}
