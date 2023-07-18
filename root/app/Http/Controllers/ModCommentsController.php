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

class ModCommentsController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Comment::class, Mod::class);
    }

    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, Mod $mod)
    {
        $this->authorize('createComment', $mod);

        return CommentService::store($request, $mod);
    }

    public function show(Comment $comment)
    {
        return $comment;
    }

    public function subscribe(Mod $mod)
    {
        CommentService::subscribe($mod);
    }

    public function unsubscribe(Mod $mod)
    {
        CommentService::unsubscribe($mod);
    }
}
