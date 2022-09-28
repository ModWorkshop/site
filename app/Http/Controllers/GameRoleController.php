<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\RoleResource;
use App\Models\Game;
use App\Models\GameRole;
use App\Models\Permission;
use Arr;
use Illuminate\Http\Request;

class GameRoleController extends Controller
{
    public function __construct() {
        $this->authorizeResource([GameRole::class, 'game'], 'game_role, game');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request, Game $game)
    {
        $val = $request->validate([
            'only_assignable' => 'boolean|nullable',
            'limit' => 'integer|min:1|max:50'
        ]);

        return RoleResource::collection(GameRole::queryGet($val, function($query, $val) use ($game) {
            if ($val['only_assignable'] ?? false) {
                $query->where('id', '!=', 1);
            }

            $query->where('game_id', $game->id);
        }));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Game $game)
    {
        return $this->update($request, $game);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Game $game, GameRole $gameRole)
    {
        $gameRole->loadMissing('permissions');
        return new RoleResource($gameRole);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Game $game, GameRole $gameRole=null)
    {
        $val = $request->validate([
            'name' => 'string|min:3|max:100',
            'tag' => 'string|nullable|min:3|max:100',
            'desc' => 'string|nullable|max:1000',
            'color' => 'string|nullable|max:8',
            'order' => 'integer|nullable|min:-1|max:1000',
            'permissions' => 'array|nullable'
        ]);

        $val['tag'] ??= '';
        $val['desc'] ??= '';
        $val['color'] ??= '';

        $permissions = Arr::pull($val, 'permissions');
        $syncPerms = [];

        foreach (array_keys($permissions) as $id) {
            $syncPerms[] = $id;
        }
        
        if (isset($gameRole)) {
            $gameRole->update($val);
        } else {
            $biggestOrder = GameRole::orderByDesc('order')->first()?->order ?? 0;
            $val['order'] = $biggestOrder + 1;

            $gameRole = $game->roles()->create($val);
        }
        
        $gameRole->permissions()->sync($syncPerms);
        Permission::flushQueryCache(); // I assume https://github.com/renoki-co/laravel-eloquent-query-cache/issues/152
        $gameRole->load('permissions');
        $gameRole->refresh();

        return new RoleResource($gameRole);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Game $game, GameRole $gameRole)
    {
        $gameRole->delete();
    }
}