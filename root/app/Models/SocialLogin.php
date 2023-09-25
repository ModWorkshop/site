<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\SocialLogin
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $social_id
 * @property string $special_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 * @method static Builder|SocialLogin newModelQuery()
 * @method static Builder|SocialLogin newQuery()
 * @method static Builder|SocialLogin query()
 * @method static Builder|SocialLogin whereCreatedAt($value)
 * @method static Builder|SocialLogin whereId($value)
 * @method static Builder|SocialLogin whereSocialId($value)
 * @method static Builder|SocialLogin whereSpecialId($value)
 * @method static Builder|SocialLogin whereUpdatedAt($value)
 * @method static Builder|SocialLogin whereUserId($value)
 * @mixin Eloquent
 */
class SocialLogin extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getMorphClass(): string {
        return 'social_login';
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
