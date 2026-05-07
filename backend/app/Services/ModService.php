<?php
namespace App\Services;

use App\Models\Category;
use App\Models\File;
use App\Models\Game;
use App\Models\Link;
use App\Models\Mod;
use App\Models\ModDownload;
use App\Models\Model;
use App\Models\PopularityLog;
use App\Models\Setting;
use App\Models\User;
use App\Models\Visibility;
use Arr;
use Auth;
use Cache;
use Carbon\Carbon;
use Chr15k\MeilisearchAdvancedQuery\MeilisearchQuery;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Number;
use Meilisearch\Contracts\SearchQuery;
use Request;
use Spatie\QueryBuilder\QueryBuilder;
use Str;

class ModService {
    public const SORT_OPTIONS = [
        'bumped_at' => true,
        'published_at' => true,
        'daily_score' => true,
        'weekly_score' => true,
        'score' => true,
        'best_match' => true,
        'random' => true,
        'likes' => true,
        'downloads' => true,
        'views' => true,
        'name' => true,
    ];

    public static function meilisearch(array $val=[], ?Game $game=null, ?callable $filterFunc=null, ?callable $queryFunc=null) {
        $user = Auth::user();
        $modSearch = MeilisearchQuery::for(Mod::class);

        // Sorting
        $sortBy = Arr::get($val, 'sort', 'bumped_at');
        if (isset(self::SORT_OPTIONS[$sortBy])) {
            if ($sortBy === 'name') {
                $modSearch->sort('name:asc');
            } else if ($sortBy != 'best_match') {
                $modSearch->sort($sortBy.':desc');
            }
        }

        if (isset($filterFunc)) {
            $filterFunc($modSearch, $val, $game);
        }

        // User ownership
        if (!$user?->hasPermission('manage-mods', $game)) {
            $modSearch->where(function(MeilisearchQuery $search) use ($user) {
                $search->where('listed', true);

                if (isset($user)) {
                    $search->orWhere('user_id', $user->id)
                        ->orWhere('member_ids', $user->id);
                }
                return $search;
            });
        }

        // User preferences
        $includingIgnored = Arr::get($val, 'including_ignored', false);
        if (isset($user) && !$includingIgnored && !$user->hasPermission('manage-mods', $game)) {
            $modSearch->where(function($search) use ($user) {
                $blockedTags = $user->blockedTags->pluck('id')->toArray();
                $blockedUsers = $user->blockedUsers->pluck('id')->toArray();
                $ignoredGames = $user->ignoredGames->pluck('id')->toArray();
                $ignored = $user->ignoredMods->pluck('id')->toArray();

                return $search->whereNotIn('tag_ids', $blockedTags)
                    ->whereNotIn('game_id', $blockedTags)
                    ->whereNotIn('game_id', $ignoredGames)
                    ->whereNotIn('id', $ignored)
                    ->whereNotIn('user_id', $blockedUsers);
            });
        }

        // Queries/filters
        $gameId = Arr::get($val, 'game_id', $game?->id);
        if (isset($gameId)) {
            $modSearch->where('game_id', $gameId);
        }

        if (isset($val['category_id'])) {
            $cat = Category::where('id', $val['category_id'])->first();
            $modSearch->whereIn('category_id', [$cat->id, ...($cat->computed_children ?? [])]);
        }

        if (isset($val['user_id'])) {
            if ($val['collab'] ?? false) {
                $modSearch->whereIn('member_ids', $val['user_id']);
            } else {
                $modSearch->where('user_id', $val['user_id']);
            }
        }

        if (!empty($val['ids'])) {
            $modSearch->whereIn('id', $val['ids']);
        }

        if (!empty($val['categories'])) {
            $modSearch->whereIn('category_id', $val['categories']);
        }

        if (!empty($val['block_tags'])) {
            $modSearch->whereNotIn('tag_ids', $val['block_tags']);
        }

        if (!empty($val['tags'])) {
            $modSearch->whereIn('tag_ids', $val['tags']);
        }

        if (!empty($val['exclude_game_ids'])) {
            $modSearch->whereNotIn('game_id', $val['exclude_game_ids']);
        }

        if (!empty($val['block_tags'])) {
            $modSearch->whereNotIn('tag_ids',  $val['block_tags']);
        }

        $limit = Arr::get($val, 'limit', 20);

        $builder = $modSearch->search($val['query'] ?? '');

        if (isset($queryFunc)) {
            $builder->query(function(Builder $q) use ($builder, $queryFunc, $val, $game) {
                $queryFunc($q, $val, $game);
                $q->with(Mod::LIST_MOD_WITH);
                $builder->query(null); // A hack to prevent Scout from trying to count it via DB
            });
        }
        $mods = $builder->paginate(Number::clamp($limit, 1, 100));

        return $mods;
    }

    public static function meilisearchModsGuestCache(array $val=[], ?string $cacheForGuests=null, ?Game $game=null, ?callable $filterFunc=null, ?callable $queryFunc=null): LengthAwarePaginator
    {
        if (!Auth::check() && isset($cacheForGuests)) {
            $cacheKey = 'mods:'.($game ? 'game:'.$game->name : '').$cacheForGuests.'-'.APIService::hashByQuery();
            return Cache::remember($cacheKey, 30,
                fn() => self::meilisearch($val, $game, $filterFunc, $queryFunc)
            );
        } else {
            return self::meilisearch($val, $game, $filterFunc, $queryFunc);
        }
    }

    public static function dbFilteredMods(array $val, ?callable $querySetup=null, ?Closure $sortByFunc = null) {
        return Mod::with(Mod::LIST_MOD_WITH)
            ->queryGet($val, function($q) use($val, $querySetup, $sortByFunc) {
                if (isset($querySetup)) {
                    $q->where(fn($q) => $querySetup($q, $val));
                }
                self::filters($q, $val, $sortByFunc);
            });
    }

    public static function filters(Builder $query, array $val, ?Closure $sortByFunc = null) {
        /** @var User */
        $user = Auth::user();

        // Sorting
        $sortBy = Arr::get($val, 'sort', 'bumped_at');
        if ((!isset($sortByFunc) || !$sortByFunc($query, $val)) && isset(self::SORT_OPTIONS[$sortBy])) {
            $sortBy = $val['sort'] ?? 'bumped_at';
            $name = $val['query'] ?? null;

            if ($sortBy === 'random') {
                $query->orderByRaw('RANDOM()');
            } else if ($sortBy === 'name' || ($sortBy == 'best_match' && empty($name))) {
                $query->orderBy('name');
            } else if ($sortBy == 'best_match') {
                $query->orderByRaw("similarity(name, ?) DESC", [$name]);
            } else if ($sortBy === 'published_at') {
                $query->orderByRaw('published_at DESC NULLS LAST');
            } else {
                $query->orderByDesc($sortBy);
            }
        }

        $gameId = Arr::get($val, 'game_id');
        $game = null;

        if (isset($gameId)) {
            $game = Game::where('id', $gameId)->first();
            APIService::setCurrentGame($game);
        } else {
            $request = request();
            $game = $request->route('game');
            if (isset($game) && get_class($game) === Game::class) {
                APIService::setCurrentGame($game);
            }
        }

        if (!isset($game)) {
            $query->with(['game']);
        }

        // User ownership
        if (!$user->hasPermission('manage-mods', $game)) {
            $query->where(function($query) use ($user) {
                // If a guest or a user that doesn't have the edit-mod permission then we should hide any invisible or suspended mod
                $query->where('visibility', Visibility::public)->whereNotNull('published_at')->where('suspended', false)->where('approved', true)->where('has_download', true);

                if (isset($user)) {
                    $query->orWhere('user_id', $user->id)
                        ->orWhereHasIn('selfMember'); //let members see mods if they've accepted their membership
                }
            });
        }

        // User preferences
        $includingIgnored = Arr::get('including_ignored', false);
        if (isset($user) && !$includingIgnored && !$user->hasPermission('manage-mods', $game)) {
            $query->where(function($query) use ($user) {
                $query->whereDoesntHaveIn('blockedByMe')
                    ->whereDoesntHaveIn('gameIgnoredByMe')
                    ->whereDoesntHaveIn('ignored')
                    ->whereDoesntHaveIn('tagsSpecial', function($q) use ($user) {
                        $q->join('blocked_tags', 'taggables.tag_id', '=', 'blocked_tags.tag_id');
                        $q->where('blocked_tags.user_id', $user->id);
                    });
            });
        }

        // Queries/filters
        if (isset($gameId)) {
            $query->where('game_id', $gameId);
        }

        if (isset($val['category_id'])) {
            $cat = Category::where('id', $val['category_id'])->first();
            $query->whereIn('category_id', [$cat->id, ...($cat->computed_children ?? [])]);
        }

        if (isset($val['user_id'])) {
            if ($val['collab'] ?? false) {
                $query->whereHasIn('members', function($q) use ($val) {
                    $q->where('user_id', $val['user_id'])->where('accepted', true)->whereIn('level', ['maintainer', 'collaborator']);
                });
            } else {
                $query->where('user_id', $val['user_id']);
            }
        }

        if (!empty($val['categories'])) {
            $query->whereIn('category_id', $val['categories']);
        }

        if (!empty($val['tags'])) {
            $query->whereHasIn('tagsSpecial', function($q) use ($val) {
                $q->whereIn('taggables.tag_id', array_map('intval', $val['tags']));
            });
        }

        if (!empty($val['exclude_game_ids'])) {
            $query->whereNotIn('game_id', $val['exclude_game_ids']);
        }

        if (!empty($val['block_tags'])) {
            $query->whereDoesntHaveIn('tagsSpecial', function($q) use ($val) {
                $q->whereIn('taggables.tag_id', array_map('intval', $val['block_tags']));
            });
        }
    }

    // More lightweight version of filters that skips on the whole options
    public static function viewFilters($query, bool $forceGuest=false) {
        $query->where(function($query) use ($forceGuest) {
            $user = $forceGuest ? null : Auth::user();

            // If a guest or a user that doesn't have the edit-mod permission then we should hide any invisible or suspended mod
            if ($user?->hasPermission('manage-mods', APIService::currentGame())) {
                return;
            }

            $query->where('visibility', Visibility::public)->whereNotNull('published_at')->where('suspended', false)->where('approved', true)->where('has_download', true);

            if (isset($user)) {
                $query->orWhere('user_id', $user->id);

                //let members see mods if they've accepted their membership
                if ($opt['check_members'] ?? true) {
                    $query->orWhereHasIn('selfMember');
                }
            }
         });

         return $query;
    }



    /**
     * Uploads a file while checking if the mod didn't exceed the allowed size.
     */
    public static function attemptUpload(Mod $mod, UploadedFile $file)
    {

        $storageSize = $mod->allowed_storage || Setting::getValue('mod_storage_size');

        if (isset($mod->user->hasSupporterPerks)) {
            $storageSize = max($storageSize,  Setting::getValue('supporter_mod_storage_size'));
        }

        $fileSize = $file->getSize();

        $files = $mod->files;
        $accumulatedSize = $fileSize;
        if ($files) {
            foreach ($files as $f) {
                $accumulatedSize += $f->size;
            }
        }

        if ($accumulatedSize > $storageSize) {
            abort(406, 'Reached maximum allowed storage usage for the mod!');
        }

        // Takes the file name, removes the part before the dot and keeps the rest. Allowing for file names like tar.gz
        $fileType = Utils::safeFileType($file->getClientOriginalName());
        $fileName = $mod->id.'_'.Auth::user()->id.'_'.Str::random(40).(!empty($fileType) ? '.'.$fileType : '');
        $file->storePubliclyAs('mods/files', $fileName);

        return [$file, $fileName, $fileType];
    }

    /**
     * Register Download
     *
     * Registers a download for a mod, doesn't let you 'download' it twice
     * Works with guests
     */
    public static function registerDownload(File|Link $downloadable)
    {
        $user = Auth::user();
        $ip = Request::ip();

        $mod = $downloadable->mod;
        PopularityLog::log($mod, 'down');

        /**
         * @var Builder
         */
        $downloads = $downloadable->downloadsRelation();
        if (isset($user) && $downloads->where('user_id', $user->id)->exists()) {
            return;
        } else if ($downloads->where('ip_address', $ip)->exists()) {
            return;
        }

        // Create download for file or link
        Model::withoutTimestamps(fn () => $downloadable->increment('downloads'));

        if (isset($user)) {
            $downloadable->downloadsRelation()->create(['created_at' => Carbon::now(), 'user_id' => $user->id, 'ip_address' => $ip]);
        } else {
            $downloadable->downloadsRelation()->create(['created_at' => Carbon::now(), 'ip_address' => $ip]);
        }

        // Create download for mod
        if (isset($user) && ModDownload::where('user_id', $user->id)->where('mod_id', $mod->id)->exists()) {
            return response()->noContent(201);
        } else if (ModDownload::where('ip_address', $ip)->where('mod_id', $mod->id)->exists()) {
            return response()->noContent(201);
        }

        $download = new ModDownload([
            'created_at' => Carbon::now()
        ]);
        $download->mod_id = $mod->id;
        $download->ip_address = $ip;
        if (isset($user)) {
            $download->user_id = $user->id;
        }

        $download->save();
        $mod->increment('downloads');

        return response()->noContent(201);
    }
}
