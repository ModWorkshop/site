<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Mod;
use File;
use Illuminate\Http\Request;

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
    public function index()
    {
        //
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
            'version' => 'string|nullable|max:255'
        ]);

        $val['label'] ??= '';
        $val['desc'] ??= '';
        $val['version'] ??= '';

        $user = $request->user();
        
        if (isset($link)) {
            if ($link->version !== $val['version']) {
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
