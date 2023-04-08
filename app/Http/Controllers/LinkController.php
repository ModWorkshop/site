<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Link;
use App\Models\Mod;
use App\Services\APIService;
use File;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkController extends Controller
{
    public function __construct() {
        $mod = app(Mod::class)->resolveRouteBinding(request()->route('mod'));

        if (isset($mod)) {
            $this->authorizeResource([Link::class, 'mod'], "link, mod");
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request, Mod $mod)
    {
        return JsonResource::collection($mod->links()->queryGet($request->val()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Mod $mod)
    {
        return $this->update($request, $mod);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Mod $mod, Link $link)
    {
        return $link;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mod $mod, Link $link=null)
    {
        $val = $request->validate([
            'name' => 'required|min:3|max:255',
            'url' => 'required|url|min:3|max:1000',
            'desc' => 'string|nullable|max:1000',
            'label' => 'string|nullable|max:100',
            'version' => 'string|nullable|max:255',
            'image_id' => 'int|nullable|exists:images,id'
        ]);

        APIService::nullToEmptyStr($val, 'label', 'desc', 'version');

        $user = $request->user();
        
        if (isset($link)) {
            if (isset($val['version']) && $link->version !== $val['version']) {
                $mod->bump(true);
            }
            $link->update($val);
        } else {
            $val['user_id'] = $user->id;
            $link = $mod->links()->create($val);
            $mod->bump(false);
            $mod->refresh();
            $mod->calculateFileStatus();
        }

        return $link;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mod $mod, Link $link)
    {
        $link->delete();
    }
}
