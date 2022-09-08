<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FollowedMod
 *
 * @property int $id
 * @property int $user_id
 * @property int $mod_id
 * @property bool $notify
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod query()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod whereNotify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod whereUserId($value)
 * @mixin \Eloquent
 */
class FollowedMod extends Model
{
    use HasFactory;

    protected $guarded = [];
}
