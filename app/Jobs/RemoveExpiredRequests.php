<?php

namespace App\Jobs;

use App\Models\ModMember;
use App\Models\TransferRequest;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveExpiredRequests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //Deletes member and transfer requests if they are not accepted within a day
        $now = Carbon::now();
        $aDayAgo = $now->subHours(24);

        $members = ModMember::where('accepted', false)->where('created_at', '<=', $aDayAgo->toDateTimeString())->get();
        foreach ($members as $member) {
            $member->delete();
        }

        $transfers = TransferRequest::where('created_at', '<=', $aDayAgo->toDateTimeString())->get();
        foreach ($transfers as $transfer) {
            $transfer->delete();
        }
    }
}
