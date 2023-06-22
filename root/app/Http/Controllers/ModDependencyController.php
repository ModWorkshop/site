<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\Mod;
use App\Services\DependencyService;
use Illuminate\Http\Request;

class ModDependencyController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Dependency::class, Mod::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Mod $mod)
    {
        return DependencyService::store($request, $mod);
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
        return DependencyService::update($request, $mod, $dependency);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Mod $mod, Dependency $dependency)
    {
        return DependencyService::destroy($request, $mod, $dependency);
    }
}
