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
     * The opposite of ConvertEmptyStringsToNull, this converst nulls we expect to be empty at times.
     * For example strings, if you send them as empty string, PHP doesn't know if it's null or empty string.
     *
     * @param array $arr
     * @param string $key
     * @return void
     */
    public static function nullToEmptyStr(array &$arr, string $key)
    {
        if (array_key_exists($key, $arr)) {
            $arr[$key] ??= '';
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
    public static function tryUploadFile(?UploadedFile $file, string $fileDir, ?string $oldFile=null, ?callable $onSuccess=null)
    {
        if (isset($file)) {
            if (isset($oldFile) && !str_contains($oldFile, 'http')) {
                $oldFile = preg_replace('/\?t=\d+/', '', $oldFile);
                Storage::disk('public')->delete($fileDir.'/'.$oldFile); // Delete old avatar before uploading
            }
            $path = $file->storePubliclyAs($fileDir, $file->hashName(), 'public');

            $storePath = str_replace($fileDir.'/', '', $path);
            if (isset($onSuccess)) {
                $onSuccess($storePath);
            }

            return $storePath;
        }
    }
}