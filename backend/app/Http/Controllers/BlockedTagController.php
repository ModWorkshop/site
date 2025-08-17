<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\TagResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
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
     * List blocked tags
     */
    public function index(FilteredRequest $request)
    {
        return TagResource::collectionResponse($this->user()->blockedTags()->queryGet($request->val()));
    }

    /**
     * Create a blocked tag
     */
    public function store(Request $request)
    {
        $val = $request->validate([
           'tag_id' => 'int|min:1|exists:tags,id'
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
     * Delete a blocked tag
     */
    public function destroy($id)
    {
        $this->user()->blockedTags()->detach($id);
    }
}
