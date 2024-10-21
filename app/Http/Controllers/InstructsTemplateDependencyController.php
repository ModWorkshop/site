<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\InstructsTemplate;
use App\Services\DependencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Dependencies
 */
class InstructsTemplateDependencyController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Dependency::class, InstructsTemplate::class);
    }

    /**
     * Create an instructions template dependency
     */
    public function store(Request $request, InstructsTemplate $instructsTemplate)
    {
        return DependencyService::store($request, $instructsTemplate);
    }

    /**
     * Update an instructions template dependency
     * 
     * @authenticated
     */
    public function update(Request $request, InstructsTemplate $instructsTemplate, Dependency $dependency)
    {
        return DependencyService::update($request, $instructsTemplate, $dependency);
    }

    /**
     * Delete an instructions template dependency
     * 
     * @authenticated
     */
    public function destroy(Request $request, InstructsTemplate $instructsTemplate, Dependency $dependency)
    {
        return DependencyService::destroy($request, $instructsTemplate, $dependency);
    }
}
