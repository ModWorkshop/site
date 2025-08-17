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
 * @group Comments
 */
class ThreadCommentsController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Comment::class, Thread::class);
    }

    /**
     * List thread comments
     */
    public function index(FilteredRequest $request, Thread $thread)
    {
        return CommentService::index($request, $thread, ['orderBy' => 'pinned DESC, created_at ASC']);
    }

    /**
     * Create a thread comment
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
     * Subscribe to a thread comment
     *
     * @authenticated
     */
    public function subscribe(Thread $thread)
    {
        CommentService::subscribe($thread);
    }

    /**
     * Unsubscribe to a thread comment
     *
     * @authenticated
     */
    public function unsubscribe(Thread $thread)
    {
        CommentService::unsubscribe($thread);
    }
}
