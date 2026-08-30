<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\File;
use App\Models\Mod;
use Http;
use Kiwilan\Archive\Archive;
use Spatie\TemporaryDirectory\TemporaryDirectory;

const ALLOWED_ARCHIVE_TYPES = [
    'zip' => true,
    'rar' => true,
    '7z' => true
];

/**
 * Used for reading archive files and figuring out which files/folders they contain
 */
class ScanArchivePaths implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public File $file) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info('File scanning paths');

        $file = $this->file;

        $file->archive_paths = self::scanPathsInArchive($file);
        $file->timestamps = false;

        $file->save();
    }

    public static function scanPathsInArchive(File $file) {
        if (!(ALLOWED_ARCHIVE_TYPES[$file->type] ?? false)) {
            return null;
        }

        // Temporarily store the file from URL
        $tmpDir = TemporaryDirectory::make()
            ->deleteWhenDestroyed();

        $tmpPath = $tmpDir->path($file->file);
        Http::sink($tmpPath)
            ->get($file->downloadUrl);

        try {
            $archive = Archive::read($tmpPath);
            $items = $archive->getFileItems();
            $iLoopCheck = 0;

            $paths = [];

            foreach ($items as $item) {
                $itemPath = $item->getPath();

                $paths[$itemPath] = [
                    'isDirectory' => $item->isDirectory(),
                    'size' => $item->getSize()
                ];

                // Just in case...
                if ($iLoopCheck > 20000) {
                    \Log::info('Archive contains too many files/folders or the archive is invalid', [
                        'file' => $file
                    ]);
                    return null;
                }
                $iLoopCheck++;
            }

            return $paths;
        } catch (\Throwable $th) {
            \Log::warning("Couldn't open file archive", [
                'file' => $file,
                'error' => $th->getMessage()
            ]);
        }

        return null;
    }
}
