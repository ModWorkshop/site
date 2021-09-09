<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

/**
 * @group Category
 * 
 * API routes for interacting with categories.
 * 
 * <aside class="notice">
 *  Something important to note is that games <em>are</em> categories. They simply have their parent set to 0, aka the global category.
 *  In the past mods were not aware of what game they were in. This was changed with MWS V2 and continued with V3.
 * </aside>
 */
class CategoryController extends Controller
{
    public function update(Request $request, Category $category=null)
    {
        $val = $request->validate([
            'name' => 'string|max:150|required',
            'game_id' => 'integer|min:1|nullable|exists:categories,id',
            'parent_id' => 'integer|min:1|nullable|exists:categories,id',
        ]);

        
        if (isset($category)) {
            //TODO
        } else {
            $val['last_date'] = Date::now();
            $category = Category::create($val);
        }

        return $category;
    }

    /**
     * Mod Cateogries
     *
     * @param Request $request
     * @param Category|null $game
     * @return void
     */
    public function getCategories(Request $request, Category $game=null, bool $getGames=false)
    {
        // Query parameters
        $val = $request->validate([
            'limit' => 'integer|min:1|max:1000',
            'page' => 'integer|min:1',
            //Returns only the names of the categories
            'only_names' => 'boolean',
            'include_paths' => 'boolean'
        ]);

        $q = Category::limit($val['limit'] ?? 1000)->orderBy('name');

        if (($val['only_names'] ?? false)) {
            $q->select(['id', 'name']);
        }

        if ($getGames) {
            // Since now we use relations we cannot set it simply to 0, so instead we set it to null.
            $q->whereNull('parent_id');
        } elseif (isset($game)) {
            $q->where('game_id', $game->id);
        }

        // Limit results.
        if (isset($val['page'])) {
            $q->paginate($val['page']);
        }

        $categories = $q->get();

        $incPaths = $val['include_paths'] ?? false;

        if ($incPaths) {
            $categories->append('path');
        }

        return $categories;
    }

    /**
     * Games
     *
     * @return void
     */
    public function getGames(Request $request)
    {
        return $this->getcategories($request, null, true);
    }

    /**
     * Game
     * 
     * Returns a single game
     *
     * @urlParam game integer required The ID of the game (category)
     * 
     * @param Category $game
     * @return void
     */
    public function getGame(Category $game)
    {
        return $game;
    }

    public function getCategory(Category $category)
    {
        return $category;
    }
}
