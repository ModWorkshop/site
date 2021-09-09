<?php
namespace App\Services;

use App\Models\Category;
use Log;

class ModService {
    static $categories = [];

    public static function categoryCrumb($category)
    {
        $id = $category['id'];
        $isGame = false;
        if (!isset($category['parent_id'])) {
            $id = $category['short_name'] || $id;
            $isGame = true;
        }

        return [
            'name' => $category['name'],
            'is_game' => $isGame,
            'href' => $isGame ? "/game/{$id}" : "/category/{$id}"
        ];
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
    static public function makeBreadcrumb(array $head, int $gameId=null, int $categoryId=null, bool $includeGame=false) : array {
        $parentCategory = null;
        $nextCrumb = null;

        // Try getting the category, if current category is the game then avoid calling the function
        $parentCategory = self::getCategory($gameId, $categoryId, $includeGame);
        if (!isset($parentCategory)) {
            return [$head];
        }

        $nextCrumb =  self::categoryCrumb($parentCategory);

        if (!isset($parentCategory['parent_id'])) { //It's a game
            if ($includeGame) {
                return [
                    $head,
                    $nextCrumb
                ];
            } else {
                return [$head];
            }
        } else {
            return [
                $head,
                ...self::makeBreadcrumb($nextCrumb, $gameId, $parentCategory['parent_id'])
            ];
        }
    }

    /**
     * Gets a category by its ID
     * Shouldn't be used directly, this is only used for cases we need to loop through many categories.
     * For example makeBreadcrumb
     *
     * @param Category $game To reduce how many categories we need to loop, we only want to focus on a single game at a time
     * @param integer $categoryId
     * @return array
     */
    static function getCategory(int $gameId, int $categoryId=null, bool $includeGame=false) {
        if (!isset(self::$categories[$gameId])) {
            $query = Category::where('game_id', $gameId);
            
            Log::debug('Getting categories');
            if ($includeGame) {
                $query->orWhere('id', $gameId);
            }

            self::$categories[$gameId] = $query->get()->each->setAppends([])->toArray();
        }
        
        $categories = self::$categories[$gameId];

        foreach ($categories as $category) {
            if ($categoryId === $category['id']) {
                return $category;
            }
        }

        return null;
    }
}