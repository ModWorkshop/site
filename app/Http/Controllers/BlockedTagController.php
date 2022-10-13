<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockedTagController extends Controller
{
    public function __construct() {
        //Checked by middleware, only needs to be a user
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request)
    {
        return TagResource::collection($this->user()->blockedTags()->queryGet($request->val()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $val = $request->validate([
           'tag_id' => 'int|min:0|exists:tags,id' 
        ]);
        
        $blockedTags = $this->user()->blockedTags();
        $tagId = $val['tag_id'];

        if ($blockedTags->where('tag_id', $tagId)->exists()) {
            abort(409, 'Already blocked');
        } else {
            $blockedTags->attach($tagId);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->user()->blockedTags()->detach($id);
    }
}
