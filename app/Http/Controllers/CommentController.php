<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Mod;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Mod $mod)
    {
        $val = $request->validate([
            'page' => 'integer|min:1',
        ]);

        $val['page'] ??= 1;

        $comments = $mod->comments()->paginate(page: (int)$val['page'], perPage: 100);

        return CommentResource::collection($comments);
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
            'content' => 'string|required|min:3|max:1000',
            'reply_to' => 'integer|nullable|min:1|exists:comments,id,reply_to,NULL'
        ]);
        
        $val['user_id'] = Auth::user()->id;
        /**
         * @var Comment
         */
        $comment = $mod->comments()->create($val);

        return new CommentResource($comment);
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
        $val = $request->validate([
            'content' => 'string|required_without:pinned|min:3|max:1000',
            'pinned' => 'boolean'
        ]);

        if ($comment->reply_to && isset($val['pinned'])) {
            throw new Exception('Only regular comments can be pinned!');
        }

        /**
         * @var Comment
         */
        $comment->update($val);

        return new CommentResource($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Comment  $comment
     */
    public function destroy(Mod $mod, Comment $comment)
    {
        $comment->delete();
    }
}
