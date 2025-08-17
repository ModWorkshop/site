<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\BlockedTag
 *
 * @property int $id
 * @property int $user_id
 * @property int $tag_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|BlockedTag newModelQuery()
 * @method static Builder|BlockedTag newQuery()
 * @method static Builder|BlockedTag query()
 * @method static Builder|BlockedTag whereCreatedAt($value)
 * @method static Builder|BlockedTag whereId($value)
 * @method static Builder|BlockedTag whereTagId($value)
 * @method static Builder|BlockedTag whereUpdatedAt($value)
 * @method static Builder|BlockedTag whereUserId($value)
 * @mixin Eloquent
 */
class BlockedTag extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getMorphClass(): string {
        return 'blocked_tag';
    }
}
