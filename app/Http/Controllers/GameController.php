<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Game;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

/**
 * @group Games
 * 
 * API routes for interacting with game sections.
 */
class GameController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Game::class, 'game');
    }

    public function update(Request $request, Game $game=null)
    {
        $val = $request->validate([
            'name' => 'string|max:150|required',
        ]);

        
        if (isset($game)) {
            //TODO
        } else {
            $val['last_date'] = Date::now();
            $game = Game::create($val);
        }

        return $game;
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

        $games = Game::queryGet($val, function(Builder $query, array $val) {
            if (($val['only_names'] ?? false)) {
                $query->select(['id', 'name']);
            }

            $query->orderBy('name');
        });

        return CategoryResource::collection($games);
    }

    public function show(Game $game)
    {
        return new CategoryResource($game);
    }

    public function delete()
    {
        
    }
}
