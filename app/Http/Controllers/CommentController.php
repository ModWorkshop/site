<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Mod;
use App\Models\Notification;
use App\Models\Thread;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Rant time:
 * Why the actual fuck does PHP not support generics?
 * Why the fuck does PHP not let me overload metohds?!?!?!
 * 
 * FUN
 * 
 * Hey at least auto properties are coming!!!! 
 */

class CommentController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Comment::class, 'comment');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function _index(FilteredRequest $request, Model $commnetable)
    {
        $comments = Comment::queryGet($request->validated(), function(Builder $query, array $val) use ($commnetable) {
            $query->orderByRaw('pinned DESC, created_at DESC');
            $query->whereMorphedTo('commentable', $commnetable);
        });

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function _store(Request $request, Model $commentable)
    {
        $val = $request->validate([
            'content' => 'string|required|min:3|max:1000',
            'reply_to' => 'integer|nullable|min:1|exists:comments,id,reply_to,NULL'
        ]);
        
        $val['user_id'] = Auth::user()->id;
        /**
         * @var Comment
         */
        $comment = $commentable->comments()->create($val);

        if ($val['user_id'] !== $commentable->user_id) {
            //TODO: implement subs for discussions

            $isReply = isset($val['reply_id']);
            $notifiable = $commentable;
            if ($isReply) {
                $notifiable = Comment::find($val['reply_id']);
            }

            Notification::send(
                notifiable: $notifiable,
                context: $comment,
                user: $isReply ? $notifiable->user : $commentable->user,
                type: $isReply ? 'comment_reply' : 'mod_comment'
            );
        }

        return new CommentResource($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param  Mod  $mod
     * @return \Illuminate\Http\Response
     */
    public function _show(Model $commentable, Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Mod  $mod
     * @param Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function _update(Request $request, Model $commentable, Comment $comment)
    {
        $user = $request->user();
        $val = $request->validate([
            'content' => 'string|required_without:pinned|min:3|max:1000',
            'pinned' => 'boolean'
        ]);

        if ($comment->reply_to && isset($val['pinned'])) {
            throw new Exception('Only regular comments can be pinned!');
        }

        //While we allow mod members to pin comments, we should NEVER allow them to edit them!
        if (isset($val['content']) && (!$user->hasPermission('edit-comment') && $comment->user->id !== $user->id)) {
            throw new Exception('You cannot edit the comment!');
        }

        /**
         * @var Comment
         */
        $comment->update($val);

        return new CommentResource($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Comment  $comment
     */
    public function _destroy(Model $commentable, Comment $comment)
    {
        $comment->delete();
    }

    /**
     * Finds the page of the comment
     *
     * @param Request $request
     * @param Model $commentable
     * @param integer $comment
     * @return void
     */
    public function _page(Request $request, Model $commentable, int $comment)
    {
        $limit = $request->query->getInt('limit', 20);

        $comments = DB::table('comments')
            ->select(['id', 'commentable_id', 'commentable_type', 'reply_to', DB::raw('row_number() over(ORDER BY pinned DESC, created_at DESC) AS position')])
            ->orderByRaw('pinned DESC, created_at DESC')
            ->whereNull('reply_to')
            ->where('commentable_type', $commentable->getMorphClass())
            ->where('commentable_id', $commentable->id);

        $comment = Comment::from($comments, 'coms')->where('coms.id', $comment)->first();

        return ceil($comment->position / $limit);
    }
}
