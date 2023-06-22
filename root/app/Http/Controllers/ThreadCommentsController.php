<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Comment;
use App\Models\Thread;
use App\Services\APIService;
use App\Services\CommentService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ThreadCommentsController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Comment::class, Thread::class);
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

        $comment = CommentService::store($request, $thread);
        $thread->increment('comment_count');
        $thread->update([
            'last_user_id' => $request->user()->id,
            'bumped_at' => Carbon::now()
        ]);

        return $comment;
    }

    public function subscribe(Thread $thread)
    {
        CommentService::subscribe($thread);
    }

    public function unsubscribe(Thread $thread)
    {
        CommentService::unsubscribe($thread);
    }
}
