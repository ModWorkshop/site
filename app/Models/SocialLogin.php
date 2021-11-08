<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SocialLogin
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $social_id
 * @property string $special_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereSocialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereSpecialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereUserId($value)
 * @mixin \Eloquent
 */
class SocialLogin extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
