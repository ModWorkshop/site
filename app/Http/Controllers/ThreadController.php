<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Forum;
use App\Models\Thread;
use App\Services\CommentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThreadController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Thread::class, 'thread');
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
            'forum_id' => 'integer|min:1|nullable|exists:forums,id',
        ]);
        
        return JsonResource::collection(Thread::queryGet($val, function($query, array $val) {
            if (isset($val['category_id'])) {
                $query->where('category_id', $val['category_id']);
            }
            if (isset($val['category_name'])) {
                $query->orWhereRelation('category', fn($q) => $q->where('name', $val['category_name']));
            }
            if (isset($val['forum_id'])) {
                $query->where('forum_id', $val['forum_id']);
            }
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

        return $thread;
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
        return $thread;
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
            'content' => 'string|required_without:pinned|min:2|max:1000',
            'category_id' => 'integer|min:1|nullable|exists:categories,id',
            'pinned' => 'boolean'
        ]);

        $thread->update($val);
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
}
