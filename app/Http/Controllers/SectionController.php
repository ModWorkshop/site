<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

/**
 * @group Sections
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
    public function index(FilteredRequest $request)
    {
        $val = $request->val([
            //Returns only the names of the categories
            'only_names' => 'boolean',
            'include_paths' => 'boolean'
        ]);

        $sections = Section::queryGet($val, function(Builder $query, array $val) {
            if (($val['only_names'] ?? false)) {
                $query->select(['id', 'name']);
            }

            $query->orderBy('name');
        });

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
