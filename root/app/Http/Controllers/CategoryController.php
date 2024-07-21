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
use Illuminate\Http\Response;

/**
 * @group Mods
 * @subgroup Categories
 */
class CategoryController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Category::class);
    }

    /**
     * Get List of Categories
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

        $val['limit'] ??= 1000;

        $categories = Category::queryGet($val, function($query, array $val) use ($game) {
            $query->orderByRaw("display_order DESC, name ASC");

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

        return CategoryResource::collectionResponse($categories);
    }

    /**
     * Create Category
     *
     * @authenticated
     */
    public function store(Request $request, ?Game $game)
    {
        return $this->update($request, $game);
    }

    /**
     * Get Category
     */
    public function show(Category $category)
    {
        $category->load('game');
        $category->makeVisible('game');
        return new CategoryResource($category);
    }

    /**
     * Edit Category
     *
     * @authenticated
     */
    public function update(Request $request, Game $game=null, Category $category=null)
    {
        $val = $request->validate([
            'name' => 'string|max:150',
            'desc' => 'string|nullable|max:1000',
            'game_id' => 'integer|min:1|nullable|exists:games,id',
            'parent_id' => 'integer|min:1|nullable|exists:categories,id',
            'display_order' => 'integer|min:-1000|max:1000|nullable',
            'thumbnail_file' => 'nullable|max:512000|mimes:png,webp,gif,jpg',
            'approval_only' => 'boolean',
            'webhook_url' => 'string|nullable|max:1000',
            'disable_mod_managers' => 'boolean|nullable',
        ]);

        $val['game_id'] ??= $game?->id;
        APIService::nullToEmptyStr($val, 'desc', 'webhook_url', 'thumbnail_file');

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

        $thumbnailFile = Arr::pull($val, 'thumbnail_file');
        APIService::storeImage($thumbnailFile, 'categories/thumbnails', $category->thumbnail, null, fn($path) => $category->thumbnail = $path, true);

        if (!$wasCreated || isset($thumbnailFile)) {
            $category->update($val);
        }

        return $category;
    }

    /**
     * Mass updates mods of a category, only used to move mods.
     */
    public function updateMods(Request $request, Category $category)
    {
        $val = $request->validate([
            'category_id' => 'integer|min:1|nullable|exists:categories,id',
            'are_you_sure' => 'required|boolean',
        ]);

        if (!$val['are_you_sure']) {
            abort(406, 'You must tick the are you sure to do thsi action!');
        }

        $category->mods()->update([
            'category_id' => $val['category_id']
        ]);
    }

    /**
     * Deletes Category. Only if it's empty
     *
     * @authenticated
     */
    public function destroy(Category $category)
    {
        if ($category->mods()->count() === 0) {
            $category->delete();
        }
    }
}
