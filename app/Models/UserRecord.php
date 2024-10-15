<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserRecord
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $email
 * @property string|null $last_ip_address
 * @property array|null $social_logins
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|UserRecord newModelQuery()
 * @method static Builder|UserRecord newQuery()
 * @method static Builder|UserRecord query()
 * @method static Builder|UserRecord whereCreatedAt($value)
 * @method static Builder|UserRecord whereEmail($value)
 * @method static Builder|UserRecord whereId($value)
 * @method static Builder|UserRecord whereLastIpAddress($value)
 * @method static Builder|UserRecord whereSocialLogins($value)
 * @method static Builder|UserRecord whereUpdatedAt($value)
 * @method static Builder|UserRecord whereUserId($value)
 * @mixin Eloquent
 */
class UserRecord extends Model
{
    protected $guarded = [];

    protected $casts = [
        'social_logins' => 'array'
    ];

    use HasFactory;

    public function getMorphClass(): string {
        return 'user_record';
    }
}
