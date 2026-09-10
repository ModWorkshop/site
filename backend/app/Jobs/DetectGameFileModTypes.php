<?php

namespace App\Jobs;

use App\Models\Game;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class DetectGameFileModTypes implements ShouldQueue
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

        $this->game->mods()->chunk(250, function(Collection $mods) {
            foreach ($mods as $mod) {
                foreach ($mod->files as $file) {
                    $file->mod_type = $file->detectFileModType();
                    $file->timestamps = false;
                    $file->save();
                }
            }
        });

    }
}

