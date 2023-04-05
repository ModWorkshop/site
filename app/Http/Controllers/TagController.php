<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\TagResource;
use App\Models\Game;
use App\Models\Tag;
use Illuminate\Http\Request;
use Log;

/**
 * @group Tags
 */
class TagController extends Controller
{
    public function __construct(Request $request) {
        $this->authorizeGameResource(Tag::class, 'tag');
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
            'type' => 'string|in:mod,forum',
            'global' => 'string|in:true,false,0,1|nullable'
        ]);

        $tags = Tag::queryGet($val, function($query, array $val) {
            $query->where(function($q) use ($val) {
                if (isset($val['game_id'])) {
                    $q->where('game_id', $val['game_id']);
                } else if (isset($val['global']) && $val['global']) {
                    $q->orWhereNull('game_id');
                }
                if (isset($val['type'])) {
                    $q->where('type', $val['type'])->orWhere('type', '')->orWhereNull('type');
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
    public function store(Request $request, Game $game=null)
    {
        return $this->update($request, $game);
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
    public function update(Request $request, Game $game=null, Tag $tag=null)
    {
        $val = $request->validate([
            'name' => 'string|required|min:2|max:100',
            'color' => 'string|required|max:8',
            'notice' => 'string|nullable|min:3|max:1000',
            'notice_type' => 'string|nullable|in:info,warning,danger',
            'type' => 'string|nullable|in:all,forum,mod',
            'notice_localized' => 'boolean|nullable',
        ]);

        Log::info($game);

        $val['type'] ??= '';
        $val['notice_type'] ??= 'info';
        $val['notice'] ??= '';

        if (isset($tag)) {
            $tag->update($val);
        } else {
            $val['game_id'] = $game?->id;
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
