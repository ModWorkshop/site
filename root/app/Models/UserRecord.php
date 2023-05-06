<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserRecord
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $email
 * @property string|null $last_ip_address
 * @property array|null $social_logins
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRecord whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRecord whereLastIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRecord whereSocialLogins($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRecord whereUserId($value)
 * @mixin \Eloquent
 */
class UserRecord extends Model
{
    protected $guarded = [];

    protected $casts = [
        'social_logins' => 'array'
    ];

    use HasFactory;
}
