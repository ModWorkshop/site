<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $downloadable_type
 * @property int $downloadable_id
 * @property int|null $user_id
 * @property string|null $ip_address
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadableDownload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadableDownload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadableDownload query()
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadableDownload whereDownloadableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadableDownload whereDownloadableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadableDownload whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadableDownload whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadableDownload whereUserId($value)
 * @mixin \Eloquent
 */
class DownloadableDownload extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function getMorphClass(): string {
        return 'downloadable_download';
    }
}
