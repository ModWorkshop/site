<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Log;

class DeleteUnverifiedUsers implements ShouldQueue
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
        use Carbon\Carbon;
        use Illuminate\Support\Collection;

        $aDayAgo = Carbon::now()->subHours(24);
        $aMonthAgo = Carbon::now()->subMonth(1);

        User::whereNotNull('email')
            ->where('activated', false)
            ->where('created_at', '<=', $aDayAgo->toDateTimeString())
            ->where('created_at', '>=', $aMonthAgo->toDateTimeString()) //Just in case ignore older accounts
            ->orderBy('id')
            ->chunk(1000, function(Collection $users) use ($aDayAgo) {
                foreach ($users as $user) {
                    $user->delete(); // After all checks we will delete the account.
                }
            }
        );
    }
}
