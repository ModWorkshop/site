<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Mod;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Comment::class, 'comment');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request, Mod $mod)
    {
        $comments = Comment::queryGet($request->validated(), function(Builder $query, array $val) use ($mod) {
            $query->orderByRaw('pinned DESC, created_at DESC');
            $query->whereMorphedTo('commentable', $mod);
        });

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
        $user = $request->user();
        $val = $request->validate([
            'content' => 'string|required_without:pinned|min:3|max:1000',
            'pinned' => 'boolean'
        ]);

        if ($comment->reply_to && isset($val['pinned'])) {
            throw new Exception('Only regular comments can be pinned!');
        }

        //While we allow mod members to pin comments, we should NEVER allow them to edit them!
        if (isset($val['content']) && (!$user->hasPermission('edit-comment') && $comment->user->id !== $user->id)) {
            throw new Exception('You cannot edit the comment!');
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
