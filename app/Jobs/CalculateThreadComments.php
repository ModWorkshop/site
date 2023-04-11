<?php

namespace App\Jobs;

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
        User::orderBy('id')->chunk(1000, function(Collection $users) {
            foreach ($users as $user) {
                $user->update([
                    'comments_count' => $user->comments()->count()
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
