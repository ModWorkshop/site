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

class TryActivatingUsers implements ShouldQueue
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
        User::where('activated', false)->orderBy('id')->chunk(1000, function(Collection $users) use ($aDayAgo) {
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
                }
            }
        });
    }
}
