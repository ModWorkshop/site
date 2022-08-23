<?php
namespace App\Services;

use App\Models\Category;
use App\Models\Game;
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
}