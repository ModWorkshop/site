<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Forum;
use App\Models\ForumCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForumCategoryController extends Controller
{
    public function __construct() {
        $this->authorizeResource(ForumCategory::class, 'forum_category');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val([
            'forum_id' => 'integer|min:1|exists:forums,id'
        ]);

        return JsonResource::collection(ForumCategory::queryGet($val, function($query, array $val) {
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
        return $this->update($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ForumCategory $forumCategory)
    {
        return $forumCategory;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ForumCategory $forumCategory=null)
    {
        $val = $request->validate([
            'name' => 'string|nullable|min:3|max:150',
            'emoji' => 'string|nullable|max:1',
            'desc' => 'string|nullable|max:1000|',
            'forum_id' => 'integer|min:1|exists:forums,id'
        ]);
 
        if (isset($forumCategory)) {
            $forumCategory->update($val);
        } else {
            $forumCategory = ForumCategory::create($val);
        }

        return $forumCategory;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ForumCategory $forumCategory)
    {
        $forumCategory->delete();
    }
}
