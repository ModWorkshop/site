<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Permissions
 */
class PermissionController extends Controller
{
    public function __construct() {
    }

    /**
     * List permissions
     */
    public function index()
    {
        return PermissionResource::collectionResponse(Permission::orderBy('name')->paginate(100));
    }

    /**
     * Get a permission
     */
    public function show(Permission $perm)
    {
        return $perm;
    }
}
