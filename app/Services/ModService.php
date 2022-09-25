<?php
namespace App\Services;

use App\Models\Category;
use App\Models\Game;
use App\Models\Visibility;
use Auth;
use DB;
use Illuminate\Database\Query\Builder;
use Log;

class ModService {
    static $categories = [];

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
        /**
         * @var User
         */
        $user = Auth::user();

        $sortBy = $val['sort_by'] ?? 'bumped_at';

        $query->orderByDesc($sortBy);

        //These are global filters. Either things user has blocked or limits in general.
        $query->where(function($query) use ($val, $user) {
            //Hide blocked user's mods (unless a moderator)
            if (isset($user) && !$user->hasPermission('manage-mods')) {
                $query->whereNotExists(function($query) use ($user) {
                    $query->from('blocked_users')->select(DB::raw(1))->where('user_id', $user->id);
                    $query->whereColumn('blocked_users.block_user_id', 'mods.user_id');
                });
            }
    
            // If a guest or a user that doesn't have the edit-mod permission then we should hide any invisible or suspended mod
            if (!isset($user) || !$user->hasPermission('manage-mods')) {
                $query->where('visibility', Visibility::pub)->where('suspended', false)->where('approved', true)->where('has_download', true);
            }
            
            if (isset($user)) {
                $query->whereDoesntHave('tagsSpecial', function($q) use ($user) {
                    $q->join('blocked_tags', 'taggables.tag_id', '=', 'blocked_tags.tag_id');
                    $q->where('blocked_tags.user_id', $user->id);
                });
    
                $query->orWhere('user_id', $user->id);
    
                //let members see mods if they've accepted their membership
                $query->orWhereHas('members', function($q) use ($user) {
                    $q->where('user_id', $user->id)->where('accepted', true);
                });
            }
        });

        //These are filters the user inputted
        $query->where(function($query) use ($val, $user) {
            if (isset($val['game_id'])) {
                $query->where('game_id', $val['game_id']);
            }
    
            if (isset($val['category_id'])) {
                $query->where('category_id', $val['category_id']);
            }
    
            if (isset($val['user_id'])) {
                $query->where('user_id', $val['user_id']);
            }
    
            if (!empty($val['categories'])) {
                $query->whereIn('category_id', $val['categories']);
            }
    
            if (!empty($val['tags'])) {
                $query->whereHas('tagsSpecial', function($q) use ($val) {
                    $q->whereIn('taggables.tag_id', array_map('intval', $val['tags']));
                });
            }
    
            if (!empty($val['block_tags'])) {
                $query->whereDoesntHave('tagsSpecial', function($q) use ($val) {
                    $q->whereIn('taggables.tag_id', array_map('intval', $val['block_tags']));
                });
            }
        });
    }
}