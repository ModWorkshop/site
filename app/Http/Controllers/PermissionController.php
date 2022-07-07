<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

/**
 * @group Permissions
 */
class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Permission::all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $perm)
    {
        return $perm;
    }
}
