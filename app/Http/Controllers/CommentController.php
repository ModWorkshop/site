<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Mod;
use Auth;
use Illuminate\Http\Request;

class CommentController extends Controller
{
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
        $val = $request->validate([
            'content' => 'string|min:1|max:1000'
        ]);
        
        $val['user_id'] = Auth::user()->id;
        $val['mod_id'] = $mod->id;

        $mod->comments()->create($val);
    }

    /**
     * Display the specified resource.
     *
     * @param  Mod  $mod
     * @return \Illuminate\Http\Response
     */
    public function show(Mod $mod, Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Mod  $mod
     * @param Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mod $mod, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
