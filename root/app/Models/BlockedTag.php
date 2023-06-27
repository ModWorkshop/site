<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BlockedTag
 *
 * @property int $id
 * @property int $user_id
 * @property int $tag_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag whereUserId($value)
 * @mixin \Eloquent
 */
class BlockedTag extends Model
{
    use HasFactory;

    public function getMorphClass(): string {
        return 'blocked_tag';
    }
}
