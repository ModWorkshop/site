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
        ini_set('memory_limit', '5G');

        $files = File::whereNull('archive_paths')->limit(50)->get();
        foreach ($files as $file) {
            $file->archive_paths = ScanArchivePaths::scanPathsInArchive($file);
            $file->timestamps = false;
            $file->save();
        }
    }
}
