<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\ModDownload
 *
 * @property int $id
 * @property int $mod_id
 * @property int $user_id
 * @property string $ip_address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ModDownload newModelQuery()
 * @method static Builder|ModDownload newQuery()
 * @method static Builder|ModDownload query()
 * @method static Builder|ModDownload whereCreatedAt($value)
 * @method static Builder|ModDownload whereId($value)
 * @method static Builder|ModDownload whereIpAddress($value)
 * @method static Builder|ModDownload whereModId($value)
 * @method static Builder|ModDownload whereUpdatedAt($value)
 * @method static Builder|ModDownload whereUserId($value)
 * @mixin Eloquent
 */
class ModDownload extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function getMorphClass(): string {
        return 'mod_download';
    }
}
