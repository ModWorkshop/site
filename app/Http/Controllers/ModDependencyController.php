<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\Mod;
use App\Services\DependencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Dependencies
 */
class ModDependencyController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Dependency::class, Mod::class);
    }

    /**
     * Create a mod dependency
     *
     * @authenticated
     */
    public function store(Request $request, Mod $mod)
    {
        return DependencyService::store($request, $mod);
    }

    /**
     * Update a mod dependency
     *
     * @authenticated
     */
    public function update(Request $request, Mod $mod, Dependency $dependency)
    {
        return DependencyService::update($request, $mod, $dependency);
    }

    /**
     * Delete a mod dependency
     *
     * @authenticated
     */
    public function destroy(Request $request, Mod $mod, Dependency $dependency)
    {
        return DependencyService::destroy($request, $mod, $dependency);
    }
}
