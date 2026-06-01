<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SetUsersNeedsApprovalToFalse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        // Turns the "needs_mod_approval" off after 3 months of registeration
        User::where('needs_mod_approval', true)->where('created_at', '<', Carbon::now()->subMonths(1))->chunk(1000, function(Collection $users) {
            foreach ($users as $user) {
                $user->timestamps = false;
                $user->update([
                    'needs_mod_approval' => false
                ]);
            }
        });
    }
}
