<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Forum;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Response;

/**
 * @group Forums
 */
class ForumController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Forum::class);
    }

    /**
     * List forums
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val();

        return BaseResource::collectionResponse(Forum::queryGet($val));
    }

    /**
     * Get a forum
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
