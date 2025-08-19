<?php

namespace App\Jobs;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;

class DeleteOldAuditLogs implements ShouldQueue
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
        $aMonthAgo = Carbon::now()->subMonth(1);

        AuditLog::where('created_at', '<=', $aMonthAgo->toDateTimeString())
            ->orderBy('id')
            ->chunk(1000, function(Collection $logs) {
                foreach ($logs as $log) {
                    $log->delete();
                }
            }
        );
    }
}
