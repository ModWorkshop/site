<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ModView
 *
 * @property int $id
 * @property int $mod_id
 * @property int $user_id
 * @property string $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ModView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModView query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModView whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModView whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModView whereUserId($value)
 * @mixin \Eloquent
 */
class ModView extends Model
{
    use HasFactory;
}
