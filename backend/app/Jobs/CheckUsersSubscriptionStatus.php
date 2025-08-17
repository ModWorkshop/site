<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\APIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CheckUsersSubscriptionStatus implements ShouldQueue
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
        User::whereHas('supporter')->chunk(1000, function(Collection $users) {
            foreach($users as $user) {
                APIService::nitroCheck($user);
            }
        });
    }
}
