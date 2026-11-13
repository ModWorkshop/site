<?php

namespace App\Jobs;

use App\Models\HighlightedMod;
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
        $mods = HighlightedMod::where('expires_at', '<', now())->whereHas('mod', fn($q) => $q->where('highlighted', true));

        foreach ($mods as $mod) {
            $mod->highlighted = false;
            $mod->save();
        }
    }
}
