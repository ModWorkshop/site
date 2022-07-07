<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Section;
use App\Services\APIService;
use Date;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

/**
 * @group Categories
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request, Section $game=null)
    {
        // Query parameters
        $val = $request->val([
            'game_id' => 'integer|min:1|exists:sections,id',
            //Returns only the names of the categories
            'only_names' => 'boolean',
            'include_paths' => 'boolean'
        ]);

        $categories = Category::queryGet($val, function(Builder $query, array $val) {
            $query->orderBy('name');

            if (($val['only_names'] ?? false)) {
                $query->select(['id', 'name']);
            }
    
            if (isset($game)) {
                $val['game_id'] ??= $game->id;
            }
    
            if (!empty($val['game_id'])) {
                $query->where('game_id', $val['game_id']);
            }
        });

        $incPaths = $val['include_paths'] ?? false;
        if ($incPaths) {
            APIService::appendToItems($categories, 'path');
        }

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Category  $category
     * @return \Illuminate\Http\Response
     */
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
     * Remove the specified resource from storage.
     *
     * @param  Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }
}
