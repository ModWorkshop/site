<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\InstructsTemplate;
use App\Services\DependencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InstructsTemplateDependencyController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Dependency::class, InstructsTemplate::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, InstructsTemplate $instructsTemplate)
    {
        return DependencyService::store($request, $instructsTemplate);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, InstructsTemplate $instructsTemplate, Dependency $dependency)
    {
        return DependencyService::update($request, $instructsTemplate, $dependency);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, InstructsTemplate $instructsTemplate, Dependency $dependency)
    {
        return DependencyService::destroy($request, $instructsTemplate, $dependency);
    }
}
