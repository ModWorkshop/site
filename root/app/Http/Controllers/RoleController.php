<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Requests\UpsertRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use App\Services\APIService;
use App\Services\RoleService;
use Arr;
use Illuminate\Http\Request;
use Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Roles
 */
class RoleController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val([
            'only_assignable' => 'boolean|nullable',
            'with_permissions' => 'boolean|nullable',
        ]);

        $gameRoles = QueryBuilder::for(Role::class)
            ->allowedFilters([AllowedFilter::exact('is_vanity')])
            ->allowedIncludes('permissions')
            ->orderByDesc('order');

        return RoleResource::collection($gameRoles->queryGet($val, function($query, $val) {
            if ($val['only_assignable'] ?? false) {
                $query->where('id', '!=', 1);
                $query->where('self_assignable', true);
            }
        }));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UpsertRoleRequest $request)
    {
        return $this->update($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $role->loadMissing('permissions');
        return new RoleResource($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpsertRoleRequest $request, Role $role=null)
    {
        $val = $request->validated();
        APIService::nullToEmptyStr($val, 'name', 'tag', 'desc', 'color');

        $order = Arr::get($val, 'order');
        $isVanity = Arr::pull($val, 'is_vanity');
        $selfAssignable = Arr::pull($val, 'self_assignable');
        $permissions = Arr::pull($val, 'permissions');

        if ((isset($order) && !RoleService::canEdit($order))) {
            abort(403, 'You cannot edit or create roles with an order equal or higher than your highest');
        }

        if (isset($role)) {
            if ($role->is_vanity) {
                $val['self_assignable'] = $selfAssignable;
            }
            $role->update($val);
        } else {
            if (!isset($val['order'])) {
                $lowestOrder = Role::orderBy('order')->first()->order;
                $val['order'] = $lowestOrder - 2;
            }

            $role = new Role($val);
            $role->is_vanity = $isVanity ?? false;
            if ($role->is_vanity) {
                $role->self_assignable = $selfAssignable;
            }

            $role->save();
        }

        RoleService::reorderRoles();

        if (isset($permissions)) {
            if ($role->is_vanity) {
                $role->permissions()->sync([]); //Make sure vanity roles don't have permissions
            } else {
                $role->syncPerms(array_keys($permissions));
            }
            $role->load('permissions');
        }

        $role->refresh();

        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();
    }
}
