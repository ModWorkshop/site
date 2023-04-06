<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\ThreadResource;
use App\Models\Forum;
use App\Models\ForumCategory;
use App\Models\Tag;
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
        if ($request->route('forum')) {
            $forum = app(Forum::class)->resolveRouteBinding($request->route('forum'));
            if (isset($forum->game_id)) {
                APIService::setCurrentGame($forum->game);
            }
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

            $roleIds = [1];
            $gameRoleIds = null;

            if (isset($user)) {
                $roleIds = [1, ...Arr::pluck($user->roles, 'id')];
                $gameRoleIds = Arr::pluck($user->allGameRoles, 'id');
            }

            if (isset($val['no_pins']) && $val['no_pins']) {
                $query->orderByDesc('bumped_at');
            } else {
                $query->orderByRaw('pinned_at DESC NULLS LAST, bumped_at DESC');
            }

            if (!isset($user) || !$user->hasPermission('manage-discussions')) {
                $query->where(fn($q) => $q->whereNull('category_id')->orWhereRelation('category', function($q) use ($user, $roleIds, $gameRoleIds) {
                    $q->where(fn($q) => $q->when(isset($user))->where('threads.user_id', $user?->id)->orWhere('private_threads', false));
                    $q->where(function($q) use($roleIds, $gameRoleIds) {
                        $q->where(
                            fn($q) => $q->whereDoesntHave('roles', fn($q) => $q->where('can_view', false)->whereIn('role_id', $roleIds))
                                ->when(isset($gameRoleIds))->whereDoesntHave('gameRoles', fn($q) => $q->where('can_view', false)->whereIn('role_id', $gameRoleIds))
                        );
                        $q->orWhereHas('roles', fn($q) => $q->where('can_view', true)->whereIn('role_id', $roleIds));
                        $q->when(isset($gameRoleIds))->orWhereHas('gameRoles', fn($q) => $q->where('can_view', true)->whereIn('role_id', $gameRoleIds));
                    });
                }));
            }

            $query->where(function($query) use ($val) {
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
    public function store(Request $request, Forum $forum)
    {
        $val = $request->validate([
            'name' => 'string|min:3|max:150',
            'content' => 'string|required|min:2|max:1000',
            'announce_until' => 'date|nullable',
            'announce' => 'boolean',
            'category_id' => 'integer|min:1|nullable|exists:forum_categories,id',
        ]);

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
        if (!$canManageThreads && isset($val['announce'])) {
            abort(401);
        }

        $this->authorize('store', [Thread::class, $forum, $category]);

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
            'name' => 'string|min:3|max:150',
            'content' => 'string|required_without_all:pinned,locked|min:2|max:30000',
            'category_id' => 'integer|min:1|nullable|exists:forum_categories,id',
            'announce_until' => 'date|nullable',
            'announce' => 'boolean',
            'tag_ids' => 'array',
            'tag_ids.*' => 'integer|min:1',
            'pinned' => 'boolean|nullable',
            'locked' => 'boolean|nullable'
        ]);

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
            $thread->timestamps = false;
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

        $thread->update($val);
        $thread->load('forum.game');

        if(isset($tags)) {
            $thread->tags()->sync($tags);
            Tag::flushQueryCache();
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
