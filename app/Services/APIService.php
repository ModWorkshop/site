<?php

namespace App\Services;

use App\Http\Resources\AnnouncementResource;
use App\Models\Game;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Thread;
use App\Models\User;
use Arr;
use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
    public static function nullToEmptyStr(array &$arr, string ...$keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $arr)) {
                $arr[$key] ??= '';
            }
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

    public static function report(Request $request, Model $model)
    {
        $val = $request->validate([
            'reason' => 'string|min:3|max:1000'
        ]);

        $model->report($val['reason']);
    }

    public static function getUnseenNotifications()
    {
        $userId = Auth::user()?->id;

        if (!isset($userId)) {
            return null;
        }

        return Notification::where('user_id', $userId)->where('seen', false)->count();
    }

    public static function getAnnouncements(Game $game=null)
    {
        $announcements = Thread::where('forum_id', isset($game) ? $game->forum_id : 1)->where('announce', true)->get();

        $now = Carbon::now();
        foreach ($announcements as $annoucement) {
            if (isset($annoucement->annouce_until) && $now->greaterThan($annoucement->annouce_until)) {
                $annoucement->update(['announce' => false]);
            }
        }

        return AnnouncementResource::collection($announcements->take(2));
    }

    public static function setCurrentGame(Game $game)
    {
        User::setCurrentGame($game->id);
        $game->append('announcements');
    }

    public static function getSettings()
    {
        $query = Setting::query();

        $user = Auth::user();
        if (!$user?->hasPermission('admin')) {
            $query->where('public', true);
        }

        return Arr::pluck($query->get(), 'value', 'name');
    }
}