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
 * @group Mods
 * 
 * @subgroup Comments
 */
class ModCommentsController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Comment::class, Mod::class);
    }

    /**
     * Get List of Comments
     *
     * @return Response
     */
    public function index(FormRequest $request, Mod $mod)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:50'
        ]);

        return CommentService::index($request, $mod);
    }

    /**
     * Create Comment
     *
     * @authenticated
     */
    public function store(Request $request, Mod $mod)
    {
        $this->authorize('createComment', $mod);

        return CommentService::store($request, $mod);
    }

    /**
     * Get Comment
     */
    public function show(Comment $comment)
    {
        return $comment;
    }

    /**
     * Subscribe to Comment
     *
     * @authenticated
     */
    public function subscribe(Mod $mod)
    {
        CommentService::subscribe($mod);
    }

    /**
     * Unsubscribe from Comment
     *
     * @authenticated
     */
    public function unsubscribe(Mod $mod)
    {
        CommentService::unsubscribe($mod);
    }
}
