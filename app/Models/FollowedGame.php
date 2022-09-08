<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FollowedGame
 *
 * @property int $id
 * @property int $user_id
 * @property int $game_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame query()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame whereUserId($value)
 * @mixin \Eloquent
 */
class FollowedGame extends Model
{
    use HasFactory;
    
    protected $guarded = [];
}
