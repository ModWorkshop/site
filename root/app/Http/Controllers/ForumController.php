<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Forum;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

/**
 * @group Forum
 */
class ForumController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Forum::class);
    }

    /**
     * Get List of Forums
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val();

        return JsonResource::collection(Forum::queryGet($val));
    }

    /**
     * Get Forum
     */
    public function show(Forum $forum)
    {
        return $forum;
    }

    /**
     * @hideFromApiDocumentation
     */
    public function update(Request $request, $id)
    {
        //
    }
}
