<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\ThreadResource;
use App\Models\Forum;
use App\Models\Thread;
use App\Services\APIService;
use App\Services\CommentService;
use Arr;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThreadController extends Controller
{
    public function __construct(Request $request) {
        if ($request->route('game')) {
            $this->authorizeResource([Thread::class, 'forum'], 'thread, forum');
        } else {
            $this->authorizeResource(Thread::class, 'thread');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val([
            'category_name' => 'string|max:100|nullable',
            'category_id' => 'integer|min:1|nullable|exists:forum_categories,id',
            'tags' => 'array|max:10',
            'tags.*' => 'integer|min:1|nullable',
            'no_pins' => 'boolean|nullable',
            'forum_id' => 'integer|min:1|nullable|exists:forums,id',
        ]);
        
        return ThreadResource::collection(Thread::queryGet($val, function($query, array $val) use($request) {
            $user = $request->user();

            if (isset($val['no_pins']) && $val['no_pins']) {
                $query->orderByDesc('bumped_at');
            } else {
                $query->orderByRaw('pinned_at DESC NULLS LAST, bumped_at DESC');
            }
            $query->where(function($query) use ($val, $user) {
                if (!$user->hasPermission('manage-discussions')) {
                    $query->whereNotExists(function($query) use ($user) {
                        $query->from('blocked_users')->select(DB::raw(1))->where('user_id', $user->id);
                        $query->whereColumn('blocked_users.block_user_id', 'threads.user_id');
                    });
                }
                if (isset($val['category_id'])) {
                    $query->where('category_id', $val['category_id']);
                }
                if (isset($val['category_name'])) {
                    $query->orWhereRelation('category', fn($q) => $q->where('name', $val['category_name']));
                }
                if (isset($val['forum_id'])) {
                    $query->where('forum_id', $val['forum_id']);
                }

                if (!empty($val['tags'])) {
                    $query->whereHas('tagsSpecial', function($q) use ($val) {
                        $q->whereIn('taggables.tag_id', array_map('intval', $val['tags']));
                    });
                }
            });
        }));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $val = $request->validate([
            'name' => 'string|min:3|max:150',
            'content' => 'string|required|min:2|max:1000',
            'forum_id' => 'integer|min:1|exists:forums,id',
            'category_id' => 'integer|min:1|nullable|exists:forum_categories,id',
        ]);

        $val['user_id'] = $request->user()->id;
        $val['last_user_id'] = $val['user_id'];
        $val['bumped_at'] = Carbon::now();

        $thread = Thread::create($val);

        return new ThreadResource($thread);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Thread $thread)
    {
        $thread->load('forum.game');
        $thread->load('tags');
        $thread->load('subscribed');
        return new ThreadResource($thread);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
        $val = $request->validate([
            'content' => 'string|required_without_all:pinned,archived|min:2|max:1000',
            'category_id' => 'integer|min:1|nullable|exists:forum_categories,id',
            'tag_ids' => 'array',
            'tag_ids.*' => 'integer|min:1',
            'pinned' => 'boolean|nullable',
            'archived' => 'boolean|nullable'
        ]);

        $changePin = Arr::pull($val, 'pinned');
        $user = $request->user();
        $canEditThreads = $user->hasPermission('manage-discussions', $thread->forum->game);
        if (!$canEditThreads && $changePin) {
            abort(401);
        }
        
        if (isset($changePin)) {
            if ($changePin === true) {
                $thread->pinned_at = Carbon::now();
            } else if ($changePin === false) {
                $thread->pinned_at = null;
            }
            $thread->timestamps = false;
        }

        $changeArchive = Arr::pull($val, 'archived');
        if (isset($changeArchive)) {
            //Cannot unarchive a thread archived by a moderator!
            if ($thread->archived_by_mod && !$canEditThreads) {
                abort(401);
            }

            $thread->archived = $changeArchive;
            $thread->timestamps = false;

            //If a moderator archives this, make it so the poster cannot unarchive it.
            if ($canEditThreads && $thread->user->id !== $user->id) {
                $thread->archived_by_mod = $changeArchive;
            } 
        }

        $tags = Arr::pull($val, 'tag_ids'); // Since 'tags' isn't really inside the model, we need to pull it out.

        $thread->update($val);
        $thread->load('forum.game');

        if(isset($tags)) {
            $thread->tags()->sync($tags);
        }

        $thread->timestamps = true;

        $thread->loadMissing('tags');

        return new ThreadResource($thread);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        $thread->delete();
    }

    /**
     * Reports the resource for moderators to look at.
     */
    public function report(Request $request, Thread $thread)
    {
        APIService::report($request, $thread);
    }
}
