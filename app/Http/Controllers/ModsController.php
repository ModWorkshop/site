<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Mod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ModsController extends Controller
{
    public function view()
    {
        $mods = Mod::limit(40)->whereHas('tags', function(Builder $q) {
            $q->where('tags.id', 31);
        })->get();
        return $mods;
    }

    public function save(Request $request, Mod $mod=null)
    {
        $val = $request->validate([
            'name' => 'string|max:150|required',
            'desc' => 'string|max:30000|required',
            'version' => 'string|max:100|default:""',
            'visibility' => 'integer|min:1|max:4|required',
            'game_id' => 'integer|min:1|required|exists:categories,id',
            'category_id' => 'integer|min:1|nullable|exists:categories,id'
        ]);

        if (isset($mod)) {
            $mod->update($val);
            return Response::HTTP_OK;
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

    public function getAllCategories(Request $request)
    {
        $val = $request->validate([
            'limit' => 'integer',
        ]);

        if (isset($val['limit'])) {
            return Category::limit($val['limit'])->get();
        } else {
            return Category::all();
        }
    }
}
