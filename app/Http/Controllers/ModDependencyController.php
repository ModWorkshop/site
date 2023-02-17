<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\Mod;
use App\Services\DependecyService;
use Illuminate\Http\Request;

class ModDependencyController extends Controller
{
    public function __construct() {
        app(Mod::class)->resolveRouteBinding(request()->route('mod'));
        $this->authorizeResource([Dependency::class, 'mod'], "dependency, mod");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Mod $mod)
    {
        return DependecyService::store($request, $mod);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mod $mod, Dependency $dependency)
    {
        return DependecyService::update($request, $mod, $dependency);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Mod $mod, Dependency $dependency)
    {
        return DependecyService::destroy($request, $mod, $dependency);
    }
}
