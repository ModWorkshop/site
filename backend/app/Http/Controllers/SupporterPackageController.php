<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\BaseResource;
use App\Models\SupporterPackage;
use App\Services\APIService;
use Illuminate\Http\Request;

class SupporterPackageController extends Controller
{
    public function __construct() {
        $this->authorizeResource(SupporterPackage::class, 'supporter_package');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val();
        return BaseResource::collectionResponse(SupporterPackage::queryGet($val, fn($q) => $q->orderBy('order')));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $val = $request->validate([
            'name' => 'string|min:1',
            'order' => 'int',
            'enabled' => 'boolean',
            'package_id' => "int|required",
            'price' => 'int|required',
            'duration_type' => 'in:mo,y,w,d',
            'duration_number' => 'int|min:1|max:120',
        ]);

        return SupporterPackage::create($val);
    }

    /**
     * Display the specified resource.
     */
    public function show(SupporterPackage $supporterPackage)
    {
        return $supporterPackage;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupporterPackage $supporterPackage)
    {
        $val = $request->validate([
            'name' => 'string|min:1|nullable',
            'order' => 'int|nullable',
            'enabled' => 'boolean|nullable',
            'package_id' => "int|nullable",
            'price' => 'int|nullable',
            'duration_type' => 'in:mo,y,w,d|nullable',
            'duration_number' => 'int|min:1|max:120|nullable',
        ]);

        $supporterPackage->update($val);
        return $supporterPackage;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupporterPackage $supporterPackage)
    {
        $supporterPackage->delete();
    }
}
