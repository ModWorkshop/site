<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Comment;
use App\Models\Thread;
use App\Services\CommentService;
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
        return CommentService::index($request, $thread);
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

        return CommentService::store($request, $thread);
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
}
