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
 * API routes for interacting with game sections.
 */
class SectionController extends Controller
{
    public function update(Request $request, Section $section=null)
    {
        $val = $request->validate([
            'name' => 'string|max:150|required',
        ]);

        
        if (isset($section)) {
            //TODO
        } else {
            $val['last_date'] = Date::now();
            $section = Section::create($val);
        }

        return $section;
    }

    /**
     * Mod Cateogries
     *
     * @param Request $request
     * @param Category|null $game
     * @return void
     */
    public function index(Request $request)
    {
        // Query parameters
        $val = $request->validate([
            'limit' => 'integer|min:1|max:1000',
            'page' => 'integer|min:1',
            //Returns only the names of the categories
            'only_names' => 'boolean',
            'include_paths' => 'boolean'
        ]);

        $q = Section::limit($val['limit'] ?? 1000)->orderBy('name');

        if (($val['only_names'] ?? false)) {
            $q->select(['id', 'name']);
        }

        $sections = $q->paginate(page: $val['page'] ?? 1, perPage: 1000);

        return CategoryResource::collection($sections);
    }

    public function show(Section $game)
    {
        return new CategoryResource($game);
    }

    public function delete()
    {
        
    }
}
