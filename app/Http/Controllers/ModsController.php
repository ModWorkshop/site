<?php

namespace App\Http\Controllers;

use App\Models\Mod;
use Illuminate\Http\Request;

class ModsController extends Controller
{
    public function view()
    {
        $mods = Mod::limit(40)->get();
        return $mods;
    }

    public function save(Request $request, int $id=null)
    {
        $val = $request->validate([
            'name' => 'string|max:150|required',
            'desc' => 'string|max:30000|required'
        ]);

        if (isset($id)) {
            
        } else {
            // Never put something like $request->all(); in create.
            //Laravel may have guard for this, but there's really no reason what to so ever to give it that.
            $val['submitter_uid'] = $request->user()->id;
            $mod = Mod::create($val); // Validate handles the important stuff already.
            return $mod->toJson();
        }

        return back()->withErrors([
            'name' => 'A mod must have a name and it should not exceed 150 characters',
            'desc' => 'A mod must have a description and it should not exceed 30k character'
        ]);
    }
}
