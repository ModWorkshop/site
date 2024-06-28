<?php

namespace App\Services;

use App\Http\Resources\AnnouncementResource;
use App\Models\Ban;
use App\Models\Game;
use App\Models\Notification;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Supporter;
use App\Models\Suspension;
use App\Models\Thread;
use App\Models\User;
use App\Models\UserCase;
use Arr;
use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\Password;
use Storage;
use Jcupitt\Vips;
use Log;
use Route;

const animated = [
    'gif' => true,
    'webp' => true,
    'avif' => true,
    'jxl' => true
    // 'png' => true, https://github.com/libvips/libvips/issues/2537
    // 'apng' => true
];

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
     * Stores an UploadedFile $file into r2 storage
     *
     * @param UploadedFile|null $file
     * @param string $fileDir Where to store the image
     * @param string|null $oldFile Automatically delete old files including thumbnail
     * @param integer|null $thumbnailSize Make a thumbnail for the image
     * @param callable|null $onSuccess Callback to run after successful upload
     * @return array
     */
    public static function storeImage(UploadedFile|string|null $file, string $fileDir, ?string $oldFile=null, int $thumbnailSize=null, ?callable $onSuccess=null)
    {
        if (empty($file) || !isset($file)) {
            return null;
        }

        if (isset($oldFile) && !str_contains($oldFile, 'http')) {
            $oldFile = preg_replace('/\?t=\d+/', '', $oldFile);
            Storage::delete($fileDir.'/'.$oldFile);
            Storage::delete($fileDir.'/thumbnail_'.$oldFile);
        }

        $fileType = $file->extension();
        $opts = isset(animated[$fileType]) ? '[n=-1]' : '';
        $fileName = preg_replace('/\.[^.]+$/', '.webp', $file->hashName());

        $img = Vips\Image::newFromFile($file->path().$opts);
        $buffer = $img->writeToBuffer('.webp', ["Q" => 80]);
        Storage::put($fileDir.'/'.$fileName, $buffer);

        $thumb = null;
        $thumbBuffer = null;
        if (isset($thumbnailSize)) {
            $thumb = $img->thumbnail_image($thumbnailSize);
            $thumbBuffer = $thumb->writeToBuffer('.webp');
            Storage::put($fileDir.'/thumbnail_'.$fileName, $thumbBuffer);
        }

        if (isset($onSuccess)) {
            $onSuccess($fileName);
        }

        return [
            'image' => $img,
            'thumbnail' => $thumb,
            'name' => $fileName,
            'size' => strlen($buffer),
            'thumbnail_size' => strlen($thumbBuffer),
            'type' => 'webp'
        ];
    }
    
    /**
     * Deletes an image from the storage
     */
    public static function deleteImage(string $fileDir, ?string $file=null) {
        if (isset($file) && !str_contains($file, 'http')) {
            $file = preg_replace('/\?t=\d+/', '', $file);
            Storage::delete($fileDir.'/'.$file);
            Storage::delete($fileDir.'/thumbnail_'.$file);
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
            if (isset($annoucement->announce_until) && $now->greaterThan($annoucement->announce_until)) {
                $annoucement->update(['announce' => false]);
            }
        }

        return AnnouncementResource::collection($announcements->take(2));
    }

    public static function currentGame(): ?Game
    {
        return app('siteState')->getCurrentGame();
    }

    public static function setCurrentGame(?Game $game)
    {
        app('siteState')->setCurrentGame($game);
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

    public static function getPasswordRule()
    {
        return Password::min(12)->numbers()->mixedCase();
    }


    /**
     * Registers a game resource with also a direct resource link.
     * store method still requires a game so that's not available in the global one.
     */
    public static function resource(string $resource, string $class, string $parent, array $config=[]) {
        $reg = Route::resource("{$parent}.{$resource}", $class);
        if (isset($config['parentOptional']) && $config['parentOptional'] == true) {
            Route::resource($resource, $class)->only(['index', 'store']);
        } else {
            Route::resource($resource, $class)->only($config['selfOnly'] ?? ['index']);
        }
        if ($config['shallow'] ?? true) {
            $reg->shallow();
        }
        $reg->except(['create', 'edit', ...($config['except'] ?? [])]);

        return $reg;
    }
    public static function gameResource(string $resource, string $class, array $config=[]) {
        return self::resource($resource, $class, 'games', $config);
    }

    /**
     * Returns an array of 'data' and 'meta' (no links because that shit is a waste of space).
     * Meta contains current_page, last_page, total and per_page.
     */
    public static function paginatedResponse(ResourceCollection $collection)
    {
        $resource = $collection->resource;
        return [
            'data' => $collection,
            'meta' => [
                'current_page' => $resource->currentPage(),
                'from' => $resource->firstItem(),
                'last_page' => $resource->lastPage(),
                'per_page' => $resource->perPage(),
                'to' => $resource->lastItem(),
                'total' => $resource->total(),
            ]
        ];
    }

    /**
     * Returns a hash string that is built off the current given query. Used in caching when the user doesn't affect the data.
     */
    public static function hashByQuery()
    {
        return md5(serialize(request()->getQueryString()));
    }

    public static function adminData(Game $game = null)
    {
        $arr = [];

        $moderateUsers = Auth::user()->hasPermission('moderate-users', $game);
        $manageMods = Auth::user()->hasPermission('manage-mods', $game);

        if (!$moderateUsers && !$manageMods) {
            abort(403);
        }

        $gameQuery = fn($q) => $q->whereGameId($game->id);
        $globalQuery = fn($q) => $q->whereNull('game_id');

        if ($moderateUsers) {
            $arr['user_cases'] = UserCase::with('modUser')
                ->orderByRaw('active DESC, created_at DESC')
                ->when(isset($game), $gameQuery, $globalQuery)
                ->limit(3)
                ->get();

            $arr['reports'] = Report::orderByDesc('created_at')
                ->when(isset($game), $gameQuery)
                ->limit(3)
                ->get();

            $arr['bans'] = Ban::with(['modUser', 'user'])
                ->orderByDesc('created_at')
                ->whereActive(true)
                ->when(isset($game), $gameQuery, $globalQuery)
                ->limit(3)
                ->get();
        }

        if ($manageMods) {
            $arr['suspensions'] = Suspension::with('mod')
                ->orderByDesc('created_at')
                ->when(isset($game), function($q) use($game) {
                    $q->whereRelation('mod', fn($q) => $q->whereGameId($game->id));
                })
                ->whereStatus(true)
                ->limit(3)
                ->get();
        }

        return $arr;
    }

    public static function nitroCheck(User $user) {
        $signer = new \NitroPaySponsor\Signer(env('NITRO_TOKEN'));

        $user->nitroToken = $signer->sign([
            'siteId' => '92', // required
            'userId' => $user->id, // required
        ]);

        $subInfo = $signer->getUserSubscription($user?->id);
        $registeredSub = Supporter::where('provider', 'nitro')->where('user_id', $user->id)->first();

        if (!isset($registeredSub)) {
            if ($subInfo && $subInfo->status == 'active') {
                $registeredSub = Supporter::create([
                    'provider' => 'nitro',
                    'user_id' => $user->id
                ]);
            } else {
                return;
            }
        }

        $registeredSub->expire_date = Carbon::create($subInfo->subscribedUntil);
        $registeredSub->expired = $subInfo->status != 'active';
        $registeredSub->save();

        return $registeredSub;
    }

    public static function checkCaptcha(Request $request) {
        if (app()->isProduction()) {
            $request->validate([
                'h-captcha-response' => ['required', 'hcaptcha'],
            ], [
                'h-captcha-response' => 'You must solve the captcha to do this action!'
            ]);
        }
    }
}
