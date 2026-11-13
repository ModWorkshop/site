<?php

namespace App\Jobs;

use App\Models\Game;
use App\Models\Mod;
use App\Models\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class ScanSomeFilesArchivePaths implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(){ }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        self::scan();
    }

    public static function scan() {
        ini_set('memory_limit', '5G');

        $files = File::whereNull('archive_paths')
            ->whereIn('type', ['zip', '7z', 'rar'])
            ->limit(500)
            ->orderBy('size')
            ->get();

        foreach ($files as $file) {
            $file->archive_paths = ScanArchivePaths::scanPathsInArchive($file);
            $file->timestamps = false;
            $file->save();
        }
    }
}
