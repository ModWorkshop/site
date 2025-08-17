<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Forum;
use App\Http\Resources\BaseResource;

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
}
