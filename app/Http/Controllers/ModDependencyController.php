<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\Mod;
use App\Services\DependencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Mods
 * 
 * @subgroup Dependencies
 */
class ModDependencyController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Dependency::class, Mod::class);
    }

    /**
     * Create Dependency
     *
     * @authenticated
     */
    public function store(Request $request, Mod $mod)
    {
        return DependencyService::store($request, $mod);
    }

    /**
     * Edit Dependency
     *
     * @authenticated
     */
    public function update(Request $request, Mod $mod, Dependency $dependency)
    {
        return DependencyService::update($request, $mod, $dependency);
    }

    /**
     * Delete Dependency
     *
     * @authenticated
     */
    public function destroy(Request $request, Mod $mod, Dependency $dependency)
    {
        return DependencyService::destroy($request, $mod, $dependency);
    }
}
