<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Comment;
use App\Services\APIService;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Comments
 */
class CommentController extends Controller
{
    public function __construct() {
        $this->authorizeWithMorphParentResource(Comment::class, 'commentable');
    }

    /**
     * Get a comment
     */
    public function show(Comment $comment)
    {
        return $comment;
    }

    /**
     * Update a comment
     *
     * @authenticated
     */
    public function update(Request $request, Comment $comment)
    {
        return CommentService::update($request, $comment);
    }

    /**
     * Delete a comment
     *
     * @authenticated
     */
    public function destroy(Comment $comment)
    {
        CommentService::destroy($comment);
    }

    /**
     * Get page of a comment
     */
    public function page(Request $request, Comment $comment)
    {
        return CommentService::page($request, $comment);
    }

    /**
     * Subscribe to a comment
     *
     * @authenticated
     */
    public function subscribe(Comment $comment)
    {
        CommentService::subscribe($comment);
    }

    /**
     * Unsubscribe from a comment
     *
     * @authenticated
     */
    public function unsubscribe(Comment $comment)
    {
        CommentService::unsubscribe($comment);
    }

    /**
     * List comment replies
     */
    public function replies(FilteredRequest $request, Comment $comment)
    {
        return CommentService::index($request, $comment->commentable, ['orderBy' => 'created_at '.($comment->commentable->commentsOrder ?? 'ASC')], $comment->replies());
    }

    /**
     * @group Reports
     * Report a comment
     *
     * @authenticated
     */
    public function report(Request $request, Comment $comment)
    {
        APIService::report($request, $comment);
    }

    /**
     * Pin comment
     *
     * @authenticated
     */
    function setPinned(Request $request, Comment $comment) {
        CommentService::setPinned($request, $comment);
    }
}
