<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

/**
 * @group Category
 * 
 * API routes for interacting with categories.
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
    public function getCategories(Request $request, Section $game=null)
    {
        // Query parameters
        $val = $request->validate([
            'game_id' => 'integer|min:1|exists:sections,id',
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

        if (isset($game)) {
            $val['game_id'] ??= $game->id;
        }

        if (!empty($val['game_id'])) {
            $q->where('game_id', $val['game_id']);
        }

        $categories = $q->paginate(page: $val['page'] ?? 1, perPage: 1000);

        $incPaths = $val['include_paths'] ?? false;

        return CategoryResource::collection($categories);
    }

    public function getCategory(Category $category)
    {
        return new CategoryResource($category);
    }
}
