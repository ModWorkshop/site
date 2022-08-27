<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

/**
 * @group Tags
 */
class TagController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Tag::class, 'tag');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val([
            'game_id' => 'integer|min:1|nullable|exists:games,id',
            'global' => 'boolean|nullable'
        ]);

        $tags = Tag::queryGet($val, function($query, array $val) {
            $query->where(function($q) use ($val) {
                if (isset($val['game_id'])) {
                    $q->where('game_id', $val['game_id']);
                }
                if (isset($val['global']) && $val['global']) {
                    $q->orWhereNull('game_id');
                }
            });
        });

        return TagResource::collection($tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        return $tag;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag=null)
    {
        $val = $request->validate([
            'name' => 'string|required|min:2|max:100',
            'color' => 'string|required|max:8',
            'notice' => 'string|nullable|min:3|max:1000',
            'notice_type' => 'integer|min:1|max:3',
            'notice_localized' => 'boolean|nullable',
            'game_id' => 'integer|min:1|nullable|exists:games,id'
        ]);

        $val['notice'] ??= '';
        if (isset($tag)) {
            $tag->update($val);
        } else {
            $tag = Tag::create($val);
        }

        return $tag;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();
    }
}
