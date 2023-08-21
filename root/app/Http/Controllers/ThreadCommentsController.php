<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Comment;
use App\Models\Thread;
use App\Services\APIService;
use App\Services\CommentService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Threads
 * 
 * @subgroup Comments
 */
class ThreadCommentsController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Comment::class, Thread::class);
    }

    /**
     * Get List of Thread Comments
     *
     * @return Response
     */
    public function index(FormRequest $request, Thread $thread)
    {
        return CommentService::index($request, $thread, ['orderBy' => 'pinned DESC, created_at ASC']);
    }

    /**
     * Create Thread Comment
     * 
     * @authenticated
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

    /**
     * Subscribe to Thread Comment
     *
     * @authenticated
     */
    public function subscribe(Thread $thread)
    {
        CommentService::subscribe($thread);
    }

    /**
     * Unsubscribe to Thread Comment
     *
     * @authenticated
     */
    public function unsubscribe(Thread $thread)
    {
        CommentService::unsubscribe($thread);
    }
}
