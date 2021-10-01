<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $val = $request->validate([
            'page' => 'integer|min:1',
        ]);

        $val['page'] ??= 1;

        $roles = Role::with('permissions')->paginate((int)$val['page']);

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
    public function show($id)
    {
        //
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
            'desc' => 'string|max:1000',
            'color' => 'string|max:8',
            'order' => 'integer|min:1|max:1000'
        ]);

        if (isset($role)) {
            $role->update($val);
        } else {
            $role = Role::create($val);
        }

        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
