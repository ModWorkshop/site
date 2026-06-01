<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\IgnoredCategory;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

/**
 * @group Users
 *
 * @subgroup Ignored Games
 *
 * @authenticated
 */
class IgnoredCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List ignored games
     */
    public function index(Request $request)
    {
        $val = $request->validate([
            'limit' => 'integer|min:1|max:50'
        ]);

        $cats = $this->user()
            ->ignoredCategories()
            ->queryGet($val, fn($q) => $q->with('game'));


        foreach ($cats as $cat) {
            $cat->includeGameInPath = true;
            $cat->path = $cat->path;
        }

        return CategoryResource::collectionResponse($cats);
    }

    /**
     * Create an ignored game
     */
    public function store(Request $request, Authenticatable $user)
    {
        $val = $request->validate([
            'category_id' => 'int|min:0|exists:categories,id',
        ]);

        $userId = $user->id;
        if (IgnoredCategory::where('user_id', $userId)->where('category_id', $val['category_id'])->exists()) {
            abort(409, 'Already ignoring category');
        }

        IgnoredCategory::create(['category_id' => $val['category_id'], 'user_id' => $userId]);
    }

    /**
     * Delete an ignored game from ignored games
     */
    public function destroy(int $id)
    {
        $this->user()->ignoredCategories()->detach($id);
    }
}
