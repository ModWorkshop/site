<?php

namespace App\Jobs;

use App\Models\Mod;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class ComputeModTypesAllMods implements ShouldQueue
{
    public int $timeout = 1800; // 30 min

    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        Mod::chunk(500, function(Collection $mods) {
            foreach ($mods as $mod) {
                $mod->computedModType();
            }
        });
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
