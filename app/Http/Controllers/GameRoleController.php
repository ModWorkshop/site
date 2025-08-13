<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Requests\UpsertRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Game;
use App\Models\GameRole;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Services\APIService;
use App\Services\RoleService;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Game Roles
 */
class GameRoleController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(GameRole::class);
    }

    /**
     * List games roles
     */
    public function index(FilteredRequest $request, Game $game)
    {
        $val = $request->val([
            'only_assignable' => 'boolean|nullable',
            'with_permissions' => 'boolean|nullable',
            'limit' => 'integer|min:1|max:50'
        ]);

        $gameRoles = QueryBuilder::for($game->roles())
            ->allowedFilters([AllowedFilter::exact('is_vanity')])
            ->allowedIncludes('permissions')
            ->orderByDesc('order');

        return RoleResource::collectionResponse($gameRoles->queryGet($val, function($query, $val) {
            if ($val['only_assignable'] ?? false) {
                $query->where('id', '!=', 1);
                $query->where('self_assignable', true);
            }
        }));
    }

    /**
     * Create game role
     *
     * @authenticated
     */
    public function store(UpsertRoleRequest $request, Game $game)
    {
        return $this->update($request, $game);
    }

    /**
     * Get a game role
     */
    public function show(Game $game, GameRole $gameRole)
    {
        $gameRole->loadMissing('permissions');
        return new RoleResource($gameRole);
    }

    /**
     * Update a game role
     *
     * @authenticated
     */
    public function update(UpsertRoleRequest $request, Game $game, GameRole $gameRole=null)
    {
        $val = $request->validated();
        APIService::nullToEmptyStr($val, 'name', 'tag', 'desc', 'color');

        $user = $this->user();

        $order = Arr::get($val, 'order');
        $isVanity = Arr::pull($val, 'is_vanity');
        $selfAssignable = Arr::pull($val, 'self_assignable');
        $permissions = Arr::pull($val, 'permissions');

        $bypass = $user->hasPermission('manage-roles') || $user->hasPermission('manage-game', $game);
        if (!$bypass && ((isset($order) && !RoleService::canEditGameRole($game, $order)))) {
            abort(403, 'You cannot edit or create roles with an order equal or higher than your highest');
        }

        if (isset($gameRole)) {
            if ($gameRole->is_vanity) {
                $val['self_assignable'] = $selfAssignable;
            }
            AuditLog::logCreate($gameRole, $val);
            $gameRole->update($val);
        } else {
            if (!isset($val['order'])) {
                $lowestOrder = GameRole::orderBy('order')->first()->order;
                $val['order'] = $lowestOrder - 2;
            }

            $val['game_id'] = $game->id;
            $gameRole = new GameRole($val);
            $gameRole->is_vanity = $isVanity ?? false;
            if ($gameRole->is_vanity) {
                $gameRole->self_assignable = $selfAssignable;
            }

            $gameRole->save();

            AuditLog::logUpdate($gameRole, $val);
        }

        RoleService::reordrGameRoles($game);

        if (isset($permissions)) {
            if ($gameRole->is_vanity) {
                $gameRole->permissions()->sync([]); //Make sure vanity roles don't have permissions
            } else {
                $gameRole->syncPerms(array_keys($permissions));
            }
            $gameRole->load('permissions');
        }

        $gameRole->refresh();

        return new RoleResource($gameRole);
    }

    /**
     * Delete a game role
     *
     * @authenticated
     */
    public function destroy(Game $game, GameRole $gameRole)
    {
        $gameRole->delete();
        AuditLog::logDelete($gameRole);
    }
}
