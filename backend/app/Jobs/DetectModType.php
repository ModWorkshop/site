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

class DetectModType implements ShouldQueue
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
        ini_set('memory_limit', '2G');

        $mods = Mod::whereNull('mod_type')->limit(50)->get();

        foreach ($mods as $mod) {
            $game_definition = $mod->game->mod_types_definition;
            if (!empty($game_definition)) {
                $file = $mod->download_strictly_file;

                if (isset($file)) {
                    if (ALLOWED_ARCHIVE_TYPES[$file->type]) {
                        $mod->mod_type = $this->detectTypeByFile($file, $game_definition);
                    } else {
                        $mod->mod_type = 'unknown_unspported_file';
                    }
                } else {
                    $mod->mod_type = 'unknown_no_file';
                }
            } else { // TODO: if a game updates it, it should run a scan on all files slowly, maybe optional?
                $mod->mod_type = 'unknown_no_definition';
            }
            $mod->save();
        }
    }

    public static function detectTypeByFile(File $file, object $game_definition): string {

        // Temporarily store the file from URL
        $tmpDir = TemporaryDirectory::make()
            ->deleteWhenDestroyed();

        $tmpPath = $tmpDir->path($file->file);
        Http::sink($tmpPath)
            ->get($file->downloadUrl);

        $archive = Archive::read($tmpPath);
        $items = $archive->getFileItems();

        $iLoopCheck = 0;

        $rootPath = null;
        foreach ($items as $item) {
            $itemPath = $item->getPath();
            $splt = explode( '/', ltrim($itemPath, '/'));

            if (count($splt) === 1 && !$item->isDirectory()) {
                if (!$item->isDirectory()) {
                    return 'unknown'; // The root contains files.
                }
            }

            if ($rootPath == null) {
                $rootPath = $splt[0];
            } else if ($splt[0] !== $rootPath) { // The root contains more than a single folder
                return 'unknown';
            }

            // Just in case...
            if ($iLoopCheck > 20000) {
                return 'unknown_large';
            }
            $iLoopCheck++;
        }

        foreach ($game_definition as $kind => $def) {
            foreach ($items as $item) {
                $containsBlacklisted = false;
                $containsAny = false;

                $itemPath = $item->getPath();

                $withoutRoot = str_replace($rootPath.'/', '', $itemPath);

                if (isset($def['doesnt_contain'])) {
                    foreach ($def['doesnt_contain'] as $folder) {
                        if (str_starts_with($withoutRoot, $folder)) {
                            $containsBlacklisted = true;
                            break;
                        }
                    }
                }

                if ($containsBlacklisted) {
                    break; // Can't be this kind...
                }

                foreach ($def['contains_any'] as $folder) {
                    if (str_starts_with($withoutRoot, $folder)) {
                        $containsAny = true;
                        break;
                    }
                }

                if ($containsAny) {
                    return $kind;
                }
            }
        }



        return 'unknown';
    }
}
