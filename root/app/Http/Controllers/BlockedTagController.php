<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\TagResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

/**
 * @group Blocked Tags
 * 
 * @authenticated
 */
class BlockedTagController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    /**
     * Get List of Blocked Tags
     */
    public function index(FilteredRequest $request)
    {
        return TagResource::collection($this->user()->blockedTags()->queryGet($request->val()));
    }

    /**
     * Create Blocked Tag
     */
    public function store(Request $request)
    {
        $val = $request->validate([
           'tag_id' => 'int|min:0|exists:tags,id'
        ]);

        $blockedTags = $this->user()->blockedTags();
        $tagId = $val['tag_id'];

        if ($blockedTags->where('tag_id', $tagId)->exists()) {
            abort(409, 'Already blocked');
        } else {
            $blockedTags->attach($tagId);
        }
    }

    /**
     * Delete Blocked Tag
     */
    public function destroy($id)
    {
        $this->user()->blockedTags()->detach($id);
    }
}
