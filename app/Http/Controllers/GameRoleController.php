<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Requests\UpsertRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Game;
use App\Models\GameRole;
use App\Models\Permission;
use App\Services\APIService;
use App\Services\RoleService;
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
        $val = $request->val([
            'only_assignable' => 'boolean|nullable',
            'with_permissions' => 'boolean|nullable',
            'limit' => 'integer|min:1|max:50'
        ]);

        return RoleResource::collection(GameRole::queryGet($val, function($query, $val) {
            $query->orderByDesc('order');

            if ($val['with_permissions'] ?? false) {
                $query->with('permissions');
            }

            if ($val['only_assignable'] ?? false) {
                $query->where('id', '!=', 1);
            }
        }));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UpsertRoleRequest $request, Game $game)
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
    public function update(UpsertRoleRequest $request, Game $game, GameRole $gameRole=null)
    {
        $val = $request->validated();
        APIService::nullToEmptyStr($val, 'name', 'tag', 'desc', 'color');

        $order = Arr::get($val, 'order');
        $isVanity = Arr::pull($val, 'is_vanity');
        $permissions = Arr::pull($val, 'permissions');

        if ((isset($order) && !RoleService::canEditGameRole($game, $order))) {
            abort(403, 'You cannot edit or create roles with an order equal or higher than your highest');
        }
        
        if (isset($gameRole)) {
            $gameRole->update($val);
        } else {
            if (!isset($val['order'])) {
                $lowestOrder = GameRole::orderBy('order')->first()->order;    
                $val['order'] = $lowestOrder - 2;
            }

            $val['game_id'] = $game->id;
            $gameRole = new GameRole($val);
            $gameRole->is_vanity = $isVanity ?? false;

            $gameRole->save();
        }

        RoleService::reordrGameRoles($game);

        if (isset($permissions)) {
            if ($gameRole->is_vanity) {
                $gameRole->permissions()->sync([]); //Make sure vanity roles don't have permissions
            } else {
                $gameRole->syncPerms(array_keys($permissions));
            }
            Permission::flushQueryCache(); // I assume https://github.com/renoki-co/laravel-eloquent-query-cache/issues/152
            $gameRole->load('permissions');
        }

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