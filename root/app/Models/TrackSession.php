<?php

namespace App\Models;

use Chelout\RelationshipEvents\Traits\HasRelationshipObservables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TrackSession
 *
 * @property int $id
 * @property string $ip_address
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TrackSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrackSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrackSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|TrackSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrackSession whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrackSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrackSession whereUserId($value)
 * @mixin \Eloquent
 */
class TrackSession extends Model
{
    const CREATED_AT = null;

    protected $guarded = [];



    use HasFactory;
}
