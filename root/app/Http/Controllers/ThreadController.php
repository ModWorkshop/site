<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Requests\GetThreadRequest;
use App\Http\Resources\ThreadResource;
use App\Models\Forum;
use App\Models\ForumCategory;
use App\Models\Tag;
use App\Models\Thread;
use App\Services\APIService;
use App\Services\CommentService;
use App\Services\ThreadService;
use App\Services\Utils;
use Arr;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Response;

/**
 * @group Threads
 */
class ThreadController extends Controller
{
    public function __construct(Request $request) {
        $this->authorizeWithParentResource(Thread::class, Forum::class);
    }

    /**
     * Get List of Threads
     */
    public function index(GetThreadRequest $request)
    {
        return ThreadResource::collectionResponse(ThreadService::threads($request->val()));
    }

    /**
     * Create Thread
     *
     * @authenticated
     */
    public function store(Request $request, Forum $forum)
    {
        $val = $request->validate([
            'name' => 'string|min:3|max:150',
            'content' => 'string|required|min:2|max:30000',
            'announce_until' => 'date|nullable',
            'announce' => 'boolean',
            'tag_ids' => 'array',
            'tag_ids.*' => 'integer|min:1',
            'category_id' => 'integer|min:1|nullable|exists:forum_categories,id',
        ]);

        APIService::checkCaptcha($request);
        Utils::convertToUTC($val, 'announce_until');

        $val['user_id'] = $request->user()->id;
        $val['last_user_id'] = $val['user_id'];
        $val['bumped_at'] = Carbon::now();
        $val['forum_id'] = $forum->id;

        $category = null;
        if (isset($val['category_id'])) {
            $category = ForumCategory::find($val['category_id']);
        }

        $user = $this->user();
        $canManageThreads = $user->hasPermission('manage-discussions', $forum->game);
        if (!$canManageThreads && isset($val['announce']) && $val['announce']) {
            abort(401);
        }

        $this->authorize('store', [Thread::class, $forum, $category]);

        $tags = Arr::pull($val, 'tag_ids'); // Since 'tags' isn't really inside the model, we need to pull it out.

        $thread = Thread::create($val);
        if(isset($tags)) {
            $thread->tags()->sync($tags);
        }

        $thread->load('tags');
        return new ThreadResource($thread);
    }

    /**
     * Get Thread
     */
    public function show(Thread $thread)
    {
        $thread->load(['forum.game', 'tags', 'subscribed', 'answerComment']);
        return new ThreadResource($thread);
    }

    /**
     * Edit Thread
     *
     * @authenticated
     */
    public function update(Request $request, Thread $thread)
    {
        $val = $request->validate([
            'name' => 'string|min:3|max:150',
            'content' => 'string|min:2|max:30000',
            'category_id' => 'integer|min:1|nullable|exists:forum_categories,id',
            'answer_comment_id' => 'integer|min:1|nullable|exists:comments,id',
            'announce_until' => 'date|nullable',
            'announce' => 'boolean',
            'tag_ids' => 'array',
            'tag_ids.*' => 'integer|min:1',
            'pinned' => 'boolean|nullable',
            'locked' => 'boolean|nullable'
        ]);

        Utils::convertToUTC($val, 'announce_until');

        $changePin = Arr::pull($val, 'pinned');
        $changeAnnounce = Arr::pull($val, 'announce');
        $user = $this->user();
        $canManageThreads = $user->hasPermission('manage-discussions', $thread->forum->game);
        if (!$canManageThreads && ($changePin || $changeAnnounce)) {
            abort(401);
        }

        if (isset($changePin)) {
            if ($changePin === true) {
                $thread->pinned_at = Carbon::now();
            } else if ($changePin === false) {
                $thread->pinned_at = null;
            }
        }

        if (isset($changeAnnounce)) {
            $thread->announce = $changeAnnounce;
        }

        $changeArchive = Arr::pull($val, 'locked');
        if (isset($changeArchive)) {
            //Cannot unarchive a thread locked by a moderator!
            if ($thread->locked_by_mod && !$canManageThreads) {
                abort(401);
            }

            $thread->locked = $changeArchive;
            $thread->timestamps = false;

            //If a moderator archives this, make it so the poster cannot unarchive it.
            if ($canManageThreads && $thread->user->id !== $user->id) {
                $thread->locked_by_mod = $changeArchive;
            }
        }

        $tags = Arr::pull($val, 'tag_ids'); // Since 'tags' isn't really inside the model, we need to pull it out.

        $val['edited_at'] = Carbon::now();
        $thread->update($val);
        $thread->load('forum.game');

        if(isset($tags)) {
            $thread->tags()->sync($tags);
        }

        $thread->timestamps = true;

        $thread->load(['category', 'tags']);

        return new ThreadResource($thread);
    }

    /**
     * Delete Thread
     *
     * @authenticated
     */
    public function destroy(Thread $thread)
    {
        $thread->delete();
    }

    /**
     * Report Thread
     *
     * Reports the thread for moderators to look at it.
     *
     * @authenticated
     */
    public function report(Request $request, Thread $thread)
    {
        APIService::report($request, $thread);
    }
}
