<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Game;
use App\Services\APIService;
use Arr;
use Date;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * @group Categories
 */
class CategoryController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Category::class, 'category');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request, Game $game=null)
    {
        // Query parameters
        $val = $request->val([
            'game_id' => 'integer|min:1|exists:games,id',
            //Returns only the names of the categories
            'only_names' => 'boolean',
            'include_paths' => 'boolean'
        ]);

        $val['limit'] = 1000;

        $categories = Category::queryGet($val, function($query, array $val) use ($game) {
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
        $category->load('game');
        $category->makeVisible('game');
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
            'name' => 'string|max:150',
            'desc' => 'string|nullable|max:1000',
            'game_id' => 'integer|min:1|nullable|exists:games,id',
            'parent_id' => 'integer|min:1|nullable|exists:categories,id',
            'thumbnail_file' => 'nullable|max:512000|mimes:png,webp,gif,jpg',
            'approval_only' => 'boolean',
            'webhook_url' => 'string|nullable|max:1000',
        ]);

        $val['desc'] ??= '';
        $val['webhook_url'] ??= '';

        $thumbnailFile = Arr::pull($val, 'thumbnail_file');

        $wasCreated = false;
        if (!isset($category)) {
            $val['last_date'] = Date::now();
            /**
             * @var Category
             */
            $category = Category::create($val);
            $val = [];//Empty so we don't update it again.
            $wasCreated = true;
        }

        APIService::storeImage($thumbnailFile, 'categories/thumbnails', $category->thumbnail, null, fn($path) => $category->thumbnail = $path);

        if (!$wasCreated || isset($thumbnailFile)) {
            $category->update($val);
        }

        return $category;
    }

    /**
     * Deletes the category. Only empty categories can be deleted.
     */
    public function destroy(Category $category)
    {
        if ($category->doesntHave('mods')) {
            $category->delete();
        }
    }
}
