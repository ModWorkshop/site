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
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Comment $comment)
    {
        return CommentService::destroy($comment);
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
     * Reports the resource for moderators to look at.
     */
    public function report(Request $request, Comment $comment)
    {
        APIService::report($request, $comment);
    }
}
