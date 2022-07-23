<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ModDownload
 *
 * @property int $id
 * @property int $mod_id
 * @property int $user_id
 * @property string $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload whereUserId($value)
 * @mixin \Eloquent
 */
class ModDownload extends Model
{
    use HasFactory;
}
