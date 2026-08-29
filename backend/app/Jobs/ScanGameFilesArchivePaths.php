<?php

namespace App\Jobs;

use App\Models\Game;
use App\Models\Mod;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class ScanGameFilesArchivePaths implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Game $game){ }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ini_set('memory_limit', '2G');

        Mod::chunk(250, function(Collection $mods) {
            foreach ($mods as $mod) {
                foreach ($mod->files as $file) {
                    $file->archive_paths = ScanArchivePaths::scanPathsInArchive($file);
                    $file->timestamps = false;
                    $file->save();
                }
            }
        });

    }
}
