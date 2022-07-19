<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\UploadedFile;
use Storage;

class APIService {
    /**
     * Allows for appending into paginator items
     *
     * @param Paginator $paginator
     * @param string $key
     * @return void
     */
    public static function appendToItems(Paginator $paginator, string $key)
    {
        /**
         * @var Model[]
         */
        $items = $paginator->items();
        foreach ($items as $cat) {
            $cat->append($key);
        }
    }

    /**
     * Attempts to upload a file into the server, deletes the old if $oldFile is present.
     *
     * @param File $file
     * @param string $fileDir
     * @param string $fileName
     * @param string $oldFile
     * @param callable $onSuccess
     * @return void
     */
    public static function tryUploadFile(?UploadedFile $file, string $fileDir, ?string $oldFile, ?callable $onSuccess=null)
    {
        if (isset($file)) {
            if (isset($oldFile) && !str_contains($oldFile, 'http')) {
                $oldFile = preg_replace('/\?t=\d+/', '', $oldFile);
                Storage::disk('public')->delete($oldFile); // Delete old avatar before uploading
            }
            $path = $file->storePubliclyAs($fileDir, $file->hashName(), 'public');

            if (isset($onSuccess)) {
                $onSuccess(str_replace($fileDir.'/', '', $path));
            }
        }
    }
}