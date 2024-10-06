<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadableDownload extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function getMorphClass(): string {
        return 'downloadable_download';
    }
}
