<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\TrackSession
 *
 * @property int $id
 * @property string $ip_address
 * @property int|null $user_id
 * @property Carbon|null $updated_at
 * @method static Builder|TrackSession newModelQuery()
 * @method static Builder|TrackSession newQuery()
 * @method static Builder|TrackSession query()
 * @method static Builder|TrackSession whereId($value)
 * @method static Builder|TrackSession whereIpAddress($value)
 * @method static Builder|TrackSession whereUpdatedAt($value)
 * @method static Builder|TrackSession whereUserId($value)
 * @mixin Eloquent
 */
class TrackSession extends Model
{
    const CREATED_AT = null;

    protected $guarded = [];



    use HasFactory;
}
