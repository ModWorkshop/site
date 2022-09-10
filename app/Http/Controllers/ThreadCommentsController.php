<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Comment;
use App\Models\Thread;
use App\Services\CommentService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ThreadCommentsController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Comment::class, 'comment');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request, Thread $thread)
    {
        return CommentService::index($request, $thread, ['orderBy' => 'pinned DESC, created_at ASC']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Thread $thread)
    {
        $this->authorize('createComment', $thread);

        $thread->update(['last_user_id' => $request->user()->id]);

        $comment = CommentService::store($request, $thread);
        $thread->update([
            'bumped_at' => Carbon::now()
        ]);

        return $comment;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Thread $thread, Comment $comment)
    {
        return $comment;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread, Comment $comment)
    {
        return CommentService::update($request, $thread, $comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread, Comment $comment)
    {
        return CommentService::destroy($thread, $comment);
    }

    public function subscribeComment(Thread $thread, Comment $comment)
    {
        CommentService::subscribe($comment);
    }

    public function unsubscribeComment(Thread $thread, Comment $comment)
    {
        CommentService::unsubscribe($comment);
    }

    public function subscribe(Thread $thread)
    {
        CommentService::subscribe($thread);
    }

    public function unsubscribe(Thread $thread)
    {
        CommentService::unsubscribe($thread);
    }

    public function page(Request $request, Thread $thread, Comment $comment)
    {
        return CommentService::page($request, $thread, $comment);
    }

    /**
     * Returns replies of a comment
     *
     * @param FilteredRequest $request
     * @param Mod $mod
     * @param Comment $comment
     */
    public function replies(FilteredRequest $request, Thread $thread, Comment $comment)
    {
        return CommentService::index($request, $thread, [], $comment->replies());
    }
}
