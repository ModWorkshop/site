<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

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
    /**
     * Mod Cateogries
     *
     * @param Request $request
     * @param Category|null $game
     * @return void
     */
    public function getCategories(Request $request, Category $game=null)
    {
        // Query parameters
        $val = $request->validate([
            'limit' => 'integer|min:1|max:100|default:1000',
            'page' => 'integer|min:1',
            //Returns only the names of the categories
            'only_names' => 'boolean'
        ]);

        $q = Category::query();

        // Limit results.
        $q->limit($val['limit']);
        if (isset($val['page'])) {
            $q->paginate($val['limit']);
        }


        return Category::limit($val['limit'])->get();
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
        return $game->toJson();
    }
}
