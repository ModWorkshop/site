<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Comment;
use App\Services\APIService;
use App\Services\CommentService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Comment::class, 'comment');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
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
    public function update(Request $request, Comment $comment)
    {
        return CommentService::update($request, $comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        return CommentService::destroy($comment);
    }

    public function page(Request $request, Comment $comment)
    {
        return CommentService::page($request, $comment);
    }

    public function subscribeComment(Comment $comment)
    {
        CommentService::subscribe($comment);
    }

    public function unsubscribeComment(Comment $comment)
    {
        CommentService::unsubscribe($comment);
    }

    /**
     * Returns replies of a comment
     *
     * @param FilteredRequest $request
     * @param Mod $mod
     * @param Comment $comment
     */
    public function replies(FilteredRequest $request, Comment $comment)
    {
        return CommentService::index($request, $comment->commentable, ['orderBy' => 'created_at '.($comment->commentable->commentsOrder ?? 'ASC')], $comment->replies());
    }

    /**
     * Reports the resource for moderators to look at.
     */
    public function report(Request $request, Comment $comment)
    {
        APIService::report($request, $comment);
    }
}
