<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use Arr;
use Illuminate\Http\Request;

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
    public function index(Request $request)
    {
        $val = $request->validate([
            'query' => 'string|nullable',
            'only_assignable' => 'boolean|nullable'
        ]);

        $roles = Role::with('permissions')->orderBy('order');
        
        if (isset($val['query']) && !empty($val['query'])) {
            $roles->whereRaw("name % ?", [$val['query']]);
        }

        if ($val['only_assignable'] ?? false) {
            $roles->where('id', '!=', 1);
        }
        
        $roles = $roles->paginate(perPage: 100);

        return RoleResource::collection($roles);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        return new RoleResource($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role=null)
    {
        $val = $request->validate([
            'name' => 'string|min:3|max:100',
            'tag' => 'string|min:3|max:100',
            // 'desc' => 'string|max:1000',
            'color' => 'string|nullable|max:8',
            'order' => 'integer|nullable|min:-1|max:1000',
            'permissions' => 'array|nullable'
        ]);

        $permissions = Arr::pull($val, 'permissions');
        
        if (isset($role)) {
            $role->update($val);
        } else {
            $biggestOrder = Role::orderByDesc('order')->first()->order;    
            $val['order'] = $biggestOrder + 1;

            $role = Role::create($val);
        }
        
        $role->permissions()->sync($permissions);
        $role->load('permissions');

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
