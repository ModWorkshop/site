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
        $now = Carbon::now();
        $aDayAgo = $now->subHours(24);
        $aMonthAgo = $now->subMonth(1);

        User::where(
                fn($q) => $q->where(fn($q) => $q->whereNotNull('email')->whereNull('email_verified_at'))->orWhere('activated', false)
            )
            ->where('created_at', '<=', $aDayAgo->toDateTimeString())
            ->where('created_at', '>=', $aMonthAgo->toDateTimeString()) //Just in case ignore older accounts
            ->orderBy('id')
            ->chunk(1000, function(Collection $users) use ($aDayAgo) {
                foreach ($users as $user) {
                    if ($user->socialLogins()->exists()) { // Has social login, activated.
                        if ($aDayAgo->gt($user->created_at)) { // User has not verified their email but did login through social login
                            $user->email = null;
                        }
                        $user->activated = true;
                        $user->save();
                    } else if ($user->email_verified_at) { // Email was verified, account not activated, let's activate.
                        $user->activated = true;
                        $user->save();
                    } else {
                        $user->delete(); // After all checks we will delete the account.
                    }
                }
            }
        );
    }
}
