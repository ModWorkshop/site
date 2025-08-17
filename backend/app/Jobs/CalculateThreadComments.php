<?php

namespace App\Jobs;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CalculateThreadComments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        Thread::orderBy('id')->chunk(1000, function(Collection $threads) {
            foreach ($threads as $thread) {
                $thread->update([
                    'comment_count' => $thread->comments()->count()
                ]);
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
