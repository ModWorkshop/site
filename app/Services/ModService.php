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
use DB;
use Hash;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\UploadedFile;
use Log;
use Spatie\QueryBuilder\QueryBuilder;
use Str;

class ModService {
    static $categories = [];
    
    public function mods()
    {
        QueryBuilder::for(Mod::class)->allowedIncludes(['roles']);
    }

    /**
     * Makes breadcrumb for a mod or a category.
     * We need to do this because if we do it from models we'd get a very annoying n+1 pattern
     * It would be better to literally get all categories of a game and loop them.
     * 
     * Alternative solutions I thought of was to store a column in mods & categories but this creates its own problem,
     * having to update them each time the categories change.
     *
     * @param array $head
     * @param Category $game
     * @param integer|null $categoryId
     * @return array
     */
    static public function makeBreadcrumb(Game $game=null, Category $category=null, array $arr=[], array &$loopCheck=[]) : array {
        if (isset($game)) {
            $arr = [[
                'name' => $game->name,
                'id' => $game->short_name ?? $game->id,
                'type' => 'game'
            ]];
        }

        if (isset($category)) {
            if (!isset($loopCheck[$category->id])) {
                $loopCheck[$category->id] = true;
                $arr = [
                    ...$arr,
                    ...self::makeBreadcrumb(null, $category->parent, [[
                        'name' => $category->name,
                        'id' => $category->id,
                        'type' => 'category'
                    ]], $loopCheck),
                ];
            } else {
                Log::alert('Category loop detected! Please look into the database.', $category->toArray());
            }
        }
        
        return $arr;
    }

    public static function filters($query, array $val) {
        /** @var User */
        $user = Auth::user();

        $sortBy = $val['sort'] ?? 'bumped_at';

        if ($sortBy === 'random') {
            $query->orderByRaw('RANDOM()');
        } else if ($sortBy === 'name') {
            $query->orderBy('name');
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
                $query->orWhereHasIn('members', function($q) use ($user) {
                    $q->where('user_id', $user->id)->where('accepted', true);
                });
            }
        });

        //These are filters the user inputted
        $query->where(function($query) use ($val, $gameId) {
            if (isset($gameId)) {
                $query->where('game_id', $gameId);
            }
    
            if (isset($val['category_id'])) {
                $query->where('category_id', $val['category_id']);
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

        $fileType = $file->extension();
        $fileName = $mod->id.'_'.Auth::user()->id.'_'.Str::random(40).'.'.$fileType;
        $file->storePubliclyAs('mods/files', $fileName);

        return [$file, $fileName];
    }
}