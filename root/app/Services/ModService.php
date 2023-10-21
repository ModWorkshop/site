<?php
namespace App\Services;

use App\Models\Category;
use App\Models\Game;
use App\Models\Mod;
use App\Models\Setting;
use App\Models\User;
use App\Models\Visibility;
use Arr;
use Auth;
use Cache;
use DB;
use Hash;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Log;
use Spatie\QueryBuilder\QueryBuilder;
use Str;

class ModService {
    public static function mods(array $val, callable $querySetup=null, $query=null, string $cacheForGuests=null): LengthAwarePaginator
    {
        $user = Auth::user();
        if (!isset($user) && isset($cacheForGuests)) {
            return Cache::remember('mods-'.$cacheForGuests.'-'.APIService::hashByQuery(), 30, fn() => self::_mods($val, $querySetup, $query, $cacheForGuests));
        } else {
            return self::_mods($val, $querySetup, $query, $cacheForGuests);
        }
    }

    private static function _mods(array $val, callable $querySetup=null, $query=null) {
        return QueryBuilder::for($query ?? Mod::class)
            ->with(Mod::LIST_MOD_WITH)
            ->allowedFields(Mod::$allowedFields)
            ->allowedIncludes(Mod::$allowedIncludes)
            ->queryGet($val, function($q) use($val, $querySetup) {
                if (isset($querySetup)) {
                    $querySetup($q, $val);
                }
                ModService::filters($q, $val);
            });
    }

    public static function filters($query, array $val) {
        /** @var User */
        $user = Auth::user();

        $sortBy = $val['sort'] ?? 'bumped_at';
        $name = $val['query'] ?? null;

        if (!isset($name) && $sortBy == 'best_match') {
            $sortBy = 'name';
        }

        if ($sortBy === 'random') {
            $query->orderByRaw('RANDOM()');
        } else if ($sortBy === 'name') {
            $query->orderBy('name');
        } else if ($sortBy === 'best_match') {
            $query->orderByRaw("lower(name) = lower(?) DESC, name ILIKE '%' || ? || '%' DESC, name % ? DESC", [$name, $name, $name]);
        } else {
            if ($sortBy === 'published_at') {
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
            if (isset($game)) {
                APIService::setCurrentGame($game);
            }
        }

        if (!isset($game)) {
            $query->with(['game']);
        }

        //These are global filters. Either things user has blocked or limits in general.
        $query->where(function($query) use ($user, $game, $val) {
            //Hide blocked user's mods (unless a moderator)

            if (isset($user) && (!isset($val['ignore_blocked_users']) || !$val['ignore_blocked_users']) && !$user->hasPermission('manage-mods', $game)) {
                $query->whereDoesntHaveIn('blockedByMe');
            }

            // If a guest or a user that doesn't have the edit-mod permission then we should hide any invisible or suspended mod
            if (!isset($user) || !$user->hasPermission('manage-mods', $game)) {
                $query->where('visibility', Visibility::public)->whereNotNull('published_at')->where('suspended', false)->where('approved', true)->where('has_download', true);
            }

            if (isset($user)) {
                $query->whereDoesntHaveIn('tagsSpecial', function($q) use ($user) {
                    $q->join('blocked_tags', 'taggables.tag_id', '=', 'blocked_tags.tag_id');
                    $q->where('blocked_tags.user_id', $user->id);
                });

                $query->orWhere('user_id', $user->id);

                //let members see mods if they've accepted their membership
                $query->orWhereHasIn('selfMember');
            }
        });

        //These are filters the user inputted
        $query->where(function($query) use ($val, $gameId) {
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

            if (!empty($val['block_tags'])) {
                $query->whereDoesntHaveIn('tagsSpecial', function($q) use ($val) {
                    $q->whereIn('taggables.tag_id', array_map('intval', $val['block_tags']));
                });
            }
        });
    }

    // More lightweight version of filters that skips on the whole options
    public static function viewFilters($query, array $opt = []) {
         $query->where(function($query) {
            $user = Auth::user();

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

        $fileType = $file->getClientOriginalExtension();
        $fileName = $mod->id.'_'.Auth::user()->id.'_'.Str::random(40).'.'.$fileType;
        $file->storePubliclyAs('mods/files', $fileName);

        return [$file, $fileName];
    }
}
