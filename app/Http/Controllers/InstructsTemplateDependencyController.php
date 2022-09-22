<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\InstructsTemplate;
use App\Models\Mod;
use App\Services\DependecyService;
use Illuminate\Http\Request;

class InstructsTemplateDependencyController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, InstructsTemplate $instructsTemplate)
    {
        return DependecyService::store($request, $instructsTemplate);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InstructsTemplate $instructsTemplate, Dependency $dependency)
    {
        return DependecyService::update($request, $instructsTemplate, $dependency);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, InstructsTemplate $instructsTemplate, Dependency $dependency)
    {
        return DependecyService::destroy($request, $instructsTemplate, $dependency);
    }
}
