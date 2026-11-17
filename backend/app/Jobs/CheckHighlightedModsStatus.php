<?php

namespace App\Jobs;

use App\Models\HighlightedMod;
use App\Models\Mod;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckHighlightedModsStatus implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Goes over highlighted mods and removes highlighted status from them
        $mods = Mod::where('highlighted', true)->whereDoesntHave('lastHighlight')->get();

        foreach ($mods as $mod) {
            $mod->highlighted = false;
            $mod->save();
        }
    }
}
