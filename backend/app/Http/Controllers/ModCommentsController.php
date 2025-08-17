<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Comment;
use App\Models\Mod;
use App\Services\APIService;
use App\Services\CommentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Comments
 */
class ModCommentsController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Comment::class, Mod::class);
    }

    /**
     * List mod comments
     */
    public function index(FilteredRequest $request, Mod $mod)
    {
        return CommentService::index($request, $mod);
    }

    /**
     * Create a mod comment
     * 
     * @authenticated
     */
    public function store(Request $request, Mod $mod)
    {
        $this->authorize('createComment', $mod);

        return CommentService::store($request, $mod);
    }

    /**
     * Get a mod comment
     */
    public function show(Comment $comment)
    {
        return $comment;
    }

    /**
     * Subscribe to a mod comment
     *
     * @authenticated
     */
    public function subscribe(Mod $mod)
    {
        CommentService::subscribe($mod);
    }

    /**
     * Unsubscribe from a comment
     *
     * @authenticated
     */
    public function unsubscribe(Mod $mod)
    {
        CommentService::unsubscribe($mod);
    }
}
