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
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\File;
use Log;

/**
 * @group Threads
 */
class ThreadController extends Controller
{
    public function __construct(Request $request) {
        $this->authorizeWithParentResource(Thread::class, Forum::class);
    }

    /**
     * List threads
     */
    public function index(GetThreadRequest $request)
    {
        return ThreadResource::collectionResponse(ThreadService::threads($request->val()));
    }

    /**
     * Uploads a thumbnail to a thread.
     * Currently not used
     * NOT API ROUTE!
     */
    public function uploadThumbnail(Request $request, Thread $thread) {
        // $fileSize = Setting::getValue('image_max_file_size') / 1024;

        // $val = $request->validate([
        //     'thumbnail_file' => ['nullable', File::image()->max($fileSize)]
        // ]);

        // $thumb = Arr::pull($val, 'thumbnail_file');

        // APIService::storeImage($thumb, 'threads/images', $thread->thumbnail, [
        //     'allowDeletion' => true,
        //     'size' => 256,
        //     'onSuccess' => function($path) use ($thread) {
        //         $thread->thumbnail = $path;
        //         $thread->save();
        //     }
        // ]);
    }

    /**
     * Create a thread
     *
     * @authenticated
     */
    public function store(Request $request, Forum $forum)
    {
        $val = $request->validate([
            'name' => 'string|min_strict:3|max:150',
            'content' => 'string|spam_check|required|min_strict:2|max:30000',
            'announce_until' => 'date|nullable',
            'announce' => 'boolean',
            'tag_ids' => 'array|nullable',
            'tag_ids.*' => 'integer|min:1',
            'category_id' => 'integer|min:1|required|exists:forum_categories,id',
        ]);

        APIService::checkCaptcha($request);
        $user = $this->user();

        Utils::convertToUTC($val, 'announce_until');

        $val['user_id'] = $request->user()->id;
        $val['last_user_id'] = $val['user_id'];
        $val['bumped_at'] = Carbon::now();
        $val['forum_id'] = $forum->id;
        $val['parser_version'] = 2;

        $category = ForumCategory::where('forum_id', $forum->id)->find($val['category_id']);
        $this->authorize('store', [Thread::class, $forum, $category]);

        if (isset($game)) {
            echo $user->getLastGameban($game->id)?->toJson();
        }

        if (!isset($category)) {
            $cat = ForumCategory::find($val['category_id']);
            abort(406, "Category doesn't exist or is invalid. This category belongs to the forum: {$cat->forum_id}");
        }

        $canManageThreads = $user->hasPermission('manage-discussions', $forum->game);
        if (!$canManageThreads && isset($val['announce']) && $val['announce']) {
            abort(401);
        }


        $tags = Arr::pull($val, 'tag_ids'); // Since 'tags' isn't really inside the model, we need to pull it out.

        $thread = Thread::create($val);
        if(isset($tags)) {
            $thread->tags()->sync($tags);
        }

        $thread->load('tags');

        $this->uploadThumbnail($request, $thread);

        return new ThreadResource($thread);
    }

    /**
     * Get a thread
     */
    public function show(Thread $thread)
    {
        $thread->load(['forum.game', 'tags', 'subscribed', 'answerComment']);
        return new ThreadResource($thread);
    }

    /**
     * Update a thread
     *
     * @authenticated
     */
    public function update(Request $request, Thread $thread)
    {
        $val = $request->validate([
            'name' => 'string|min_strict:3|nullable|max:150',
            'content' => 'string|spam_check|min_strict:2|nullable|max:30000',
            'category_id' => 'integer|min:1|nullable|exists:forum_categories,id',
            'forum_id' => 'integer|min:1|nullable|exists:forums,id',
            'answer_comment_id' => 'integer|min:1|nullable|exists:comments,id',
            'announce_until' => 'date|nullable',
            'announce' => 'boolean|nullable',
            'tag_ids' => 'array|nullable',
            'tag_ids.*' => 'integer|min:1',
            'pinned' => 'boolean|nullable',
            'locked' => 'boolean|nullable',
            'closed' => 'boolean|nullable'
        ]);

        Utils::convertToUTC($val, 'announce_until');
        APIService::nullToEmptyArr($val, 'tag_ids');

        $changePin = Arr::pull($val, 'pinned');
        $changeAnnounce = Arr::pull($val, 'announce');
        $changeForum = Arr::pull($val, 'forum_id');
        $changeCategry = Arr::pull($val, 'category_id');

        $user = $this->user();
        $canManageThreads = $user->hasPermission('manage-discussions', $thread->forum->game);
        if (!$canManageThreads && ($changePin || $changeAnnounce)) {
            abort(401);
        }

        $changedForum = false;
        if (isset($changeForum) && intval($changeForum) !== $thread->forum_id) {
            if (!$canManageThreads) {
                abort(401, "Cannot move thread to a different forum, please ask a moderator!");
            } else {
                $forum = Forum::where('id', $changeForum)->first();
                $thread->forum_id = $forum->id;
                if (empty($changeCategry)) {
                    abort(406, 'You must provide a category to change when moving forums!');
                }
                $changedForum = true;
            }
        }

        if ($changedForum || (isset($changeCategry) && intval($changeCategry) !== $thread->category_id)) {
            // Ensure the category belongs to the forum
            $cat = ForumCategory::where('forum_id', $thread->forum_id)->where('id', $changeCategry)->first();
            if (isset($cat)) {
                $thread->category_id = $cat->id;
            } else {
                abort(406, "Category doesn't exist or is invalid");
            }
        }

        if (isset($changePin)) {
            if ($changePin === true) {
                $thread->pinned_at = Carbon::now();
            } else if ($changePin === false) {
                $thread->pinned_at = null;
            }
        }

        if (isset($changeAnnounce)) {
            $thread->announce = (boolean)$changeAnnounce;
        }

        $changeLock = Arr::pull($val, 'locked');
        if (isset($changeLock)) {
            //Cannot unarchive a thread locked by a moderator!
            if ($thread->locked_by_mod && !$canManageThreads) {
                abort(401);
            }

            $thread->locked = boolval($changeLock);
            //If a moderator locks this, make it so the poster cannot unlock it.
            if ($canManageThreads && $thread->user->id !== $user->id) {
                $thread->locked_by_mod = $changeLock;
                AuditLog::logUpdate($thread, [
                    'locked' => $changeLock
                ]);
            }
        }

        $changeClosed = Arr::pull($val, 'closed');
        if (isset($changeClosed)) {
            //Cannot open a thread closed by a moderator!
            if ($thread->closed_by_mod && !$canManageThreads) {
                abort(401);
            }

            $thread->closed = $changeClosed;
            //If a moderator locks this, make it so the poster cannot unlock it.
            if ($canManageThreads && $thread->user->id !== $user->id) {
                $thread->closed_by_mod = $changeClosed;
                AuditLog::logUpdate($thread, [
                    'closed' => $changeClosed
                ]);
            }
        }

        $tags = Arr::pull($val, 'tag_ids'); // Since 'tags' isn't really inside the model, we need to pull it out.

        if ((isset($val['content']) && $val['content'] != $thread->content) || (isset($val['name']) && $val['name'] != $thread->name)) {
            $val['edited_at'] = Carbon::now();
        }

        $val['parser_version'] = 2;

        $thread->update($val);
        $thread->load('forum.game');

        if(isset($tags)) {
            $thread->tags()->sync($tags);
        }

        $thread->timestamps = true;

        $thread->load(['category', 'tags']);

        $this->uploadThumbnail($request, $thread);

        return new ThreadResource($thread);
    }

    /**
     * Delete a thread
     *
     * @authenticated
     */
    public function destroy(Thread $thread)
    {
        $thread->delete();
    }

    /**
     * @group Reports
     *
     * Report a thread
     * @authenticated
     */
    public function report(Request $request, Thread $thread)
    {
        APIService::report($request, $thread);
    }
}
