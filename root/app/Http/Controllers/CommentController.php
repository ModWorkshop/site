<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Comment;
use App\Services\APIService;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    public function __construct() {
        $this->authorizeWithMorphParentResource(Comment::class, 'commentable');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Comment $comment)
    {
        return $comment;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, Comment $comment)
    {
        return CommentService::update($request, $comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        CommentService::destroy($comment);
    }

    public function page(Request $request, Comment $comment)
    {
        return CommentService::page($request, $comment);
    }

    public function subscribe(Comment $comment)
    {
        CommentService::subscribe($comment);
    }

    public function unsubscribe(Comment $comment)
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
     * Report Comment
     *
     * Reports the comment for moderators to look at it.
     *
     * @authenticated
     */
    public function report(Request $request, Comment $comment)
    {
        APIService::report($request, $comment);
    }

    /**
     * Set the pinned state of a comment
     */
    function setPinned(Request $request, Comment $comment) {
        CommentService::setPinned($request, $comment);
    }

    /**
     * Sets whether the comment is an answer
     */
    function setIsAnswer(Request $request, Comment $comment) {
        CommentService::setIsAnswer($request, $comment);
    }
}
