<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\ModView
 *
 * @property int $id
 * @property int $mod_id
 * @property int $user_id
 * @property string $ip_address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ModView newModelQuery()
 * @method static Builder|ModView newQuery()
 * @method static Builder|ModView query()
 * @method static Builder|ModView whereCreatedAt($value)
 * @method static Builder|ModView whereId($value)
 * @method static Builder|ModView whereIpAddress($value)
 * @method static Builder|ModView whereModId($value)
 * @method static Builder|ModView whereUpdatedAt($value)
 * @method static Builder|ModView whereUserId($value)
 * @mixin Eloquent
 */
class ModView extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function getMorphClass(): string {
        return 'mod_view';
    }
}
