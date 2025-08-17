<?php

namespace App\Jobs;

use App\Models\File;
use App\Models\PendingFile;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Storage;

class DeleteLoosePendingFiles implements ShouldQueue
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
        $disk = Storage::disk('s3');

        if (!isset($disk)) {
            return;
        }

        // Loose pending file is a pending file that is still in the DB
        // Generally the upload process is supposed to delete the pending file after a successful upload
        // If anything fails, we must clean up here
        $aDayAgo = Carbon::now()->subHours(12);
        $files = PendingFile::where('created_at', '<=', $aDayAgo->toDateTimeString())->get();

        // Case 1 file was uploaded to temp folder, not completed within 12 hours.
        // Case 2 file was uploaded, moved to temp folder and for some reason wasn't properly applied (Possible error)
        // For case 2 we wanna ensure the file doesn't belong to any existing file, making sure it's truly loose.
        foreach ($files as $pending) {
            if ($disk->exists("temp/{$pending->file_name}")) {
                $disk->delete("temp/{$pending->file_name}");
            }
            if ($disk->exists("mods/files/{$pending->file_name}") && File::where('file', $pending->file_name)->doesntExist()) {
                $disk->delete("mods/files/{$pending->file_name}");
            }
            $pending->delete();
        }
    }
}
