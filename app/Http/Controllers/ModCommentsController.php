<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Comment;
use App\Models\Mod;
use App\Services\APIService;
use App\Services\CommentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ModCommentsController extends Controller
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
        return CommentService::index($request, $mod);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Mod $mod)
    {
        $this->authorize('createComment', $mod);

        return CommentService::store($request, $mod);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Mod $mod=null, Comment $comment)
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
    public function update(Request $request, Mod $mod, Comment $comment)
    {
        return CommentService::update($request, $mod, $comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mod $mod, Comment $comment)
    {
        return CommentService::destroy($mod, $comment);
    }

    public function page(Request $request, Mod $mod, Comment $comment)
    {
        return CommentService::page($request, $mod, $comment);
    }

    public function subscribeComment(Mod $mod, Comment $comment)
    {
        CommentService::subscribe($comment);
    }

    public function unsubscribeComment(Mod $mod, Comment $comment)
    {
        CommentService::unsubscribe($comment);
    }

    public function subscribe(Mod $mod)
    {
        CommentService::subscribe($mod);
    }

    public function unsubscribe(Mod $mod)
    {
        CommentService::unsubscribe($mod);
    }

    /**
     * Returns replies of a comment
     *
     * @param FilteredRequest $request
     * @param Mod $mod
     * @param Comment $comment
     */
    public function replies(FilteredRequest $request, Mod $mod, Comment $comment)
    {
        return CommentService::index($request, $mod, ['orderBy' => 'created_at ASC'], $comment->replies());
    }

    /**
     * Reports the resource for moderators to look at.
     */
    public function report(Request $request, Mod $mod, Comment $comment)
    {
        APIService::report($request, $comment);
    }
}
