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

    public function show(Comment $comment)
    {
        return $comment;
    }

    public function subscribe(Mod $mod)
    {
        CommentService::subscribe($mod);
    }

    public function unsubscribe(Mod $mod)
    {
        CommentService::unsubscribe($mod);
    }
}
