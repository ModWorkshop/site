<?php

namespace App\Services;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\CommentResource;
use App\Interfaces\SubscribableInterface;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\User;
use App\Traits\Subscribable;
use Arr;
use Auth;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CommentService { 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function index(FilteredRequest $request, Model $commentable, array $options=[], $replies=null)
    {
        /**
         * @var Builder
         */
        $query = null;
        if (isset($replies)) {
            $query = $replies;
        } else {
            $query = Comment::query();
        }
        

        $comments = $query->queryGet($request->validated(), function($query, array $val) use ($options, $replies, $commentable) {
            $query->with(['mentions']);
            $query->orderByRaw($options['orderBy'] ?? 'pinned DESC, created_at DESC');
            if (!isset($replies)) {
                $query->whereNull('reply_to');
            }
            $query->whereMorphedTo('commentable', $commentable);
        });

        APIService::appendToItems($comments, 'last_replies');
        APIService::appendToItems($comments, 'total_replies');

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function store(Request $request, Model $commentable, array $extraSet=null)
    {
        $val = $request->validate([
            'content' => 'string|required|min:2|max:1000',
            'mentions' => 'array',
            'mentions.*' => 'string',
            'reply_to' => 'integer|nullable|min:1|exists:comments,id,reply_to,NULL'
        ]);

        $user = Auth::user();

        //Make sure to limit this to 20 users and not include ourselves!
        $uniqueNames = array_slice(Arr::pull($val, 'mentions'), 0, 10);
        $mentionedUsers = User::whereIn('unique_name', $uniqueNames)->limit(10)->get();

        $val['user_id'] = $user->id;

        $isReply = isset($val['reply_to']);
        $replyToComment = $isReply ? Comment::find($val['reply_to']) : null;

        if ($replyToComment) {
            //Make sure that whatever we are trying to reply to is a comment that is on the same commentable object (mod/thread).
            if ($replyToComment->commentable_type !== $commentable->getMorphClass() || $replyToComment->commentable_id !== $commentable->id) {
                abort(409, "Invalid comment to reply to");
            }
            
            //Make sure we are not blocked
            if (!$user->hasPermission('edit-comment') && $replyToComment->user->blockedMe) {
                abort(401);
            }
        }

        if (isset($extraSet)) {
            $val = [...$val, ...$extraSet];
        }
        
        /**
         * @var Comment
         */
        $comment = $commentable->comments()->create($val);

        $mentionedIds = [];
        /**
         * PHP is such a pain holy fucking shit
         * @var User $mentionedUser
         */
        foreach ($mentionedUsers as $mentionedUser) {
            $mentionedIds[] = $mentionedUser->id;
            if ($mentionedUser->id !== $user->id) { //Let's not mention ourselves
                Notification::send(
                    notifiable: $comment,
                    context: $commentable,
                    user: $mentionedUser,
                    type: 'comment_mention'
                );
            }
        }

        $comment->mentions()->sync($mentionedIds);

        $notifiable = ($isReply ? $replyToComment : $commentable);
        $subs = $notifiable->subscriptions;
        foreach ($subs as $sub) {
            Notification::send(
                notifiable: $notifiable,
                context: $comment,
                user: $sub->user,
                type: "sub_{$sub->subscribable_type}"
            );
        }

        $comment->loadMissing('mentions');

        return new CommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Mod  $mod
     * @param Comment $comment
     * @return \Illuminate\Http\Response
     */
    public static function update(Request $request, Model $commentable, Comment $comment)
    {
        $user = $request->user();
        $val = $request->validate([
            'mentions' => 'array',
            'content' => 'string|required_without:pinned|min:3|max:1000',
            'pinned' => 'boolean'
        ]);

        $uniqueNames = array_slice(Arr::pull($val, 'mentions'), 0, 10);
        $mentionedUsers = User::whereIn('unique_name', $uniqueNames)->limit(10)->without('roles')->get('id');
        $mentionedIds = [];
        foreach ($mentionedUsers as $mentionedUser) {
            $mentionedIds[] = $mentionedUser->id;
        }

        //Update the mentions, but avoid sending any notifications to avoid spam.
        $comment->mentions()->sync($mentionedIds);

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
    public static function destroy(Model $commentable, Comment $comment)
    {
        $comment->delete();
    }

    /**
     * Finds the page of the comment
     */
    public static function page(Request $request, Model $commentable, Comment $comment)
    {
        $limit = $request->query->getInt('limit', 20);

        $comments = DB::table('comments')
            ->select(['id', 'commentable_id', 'commentable_type', 'reply_to', DB::raw('row_number() over(ORDER BY pinned DESC, created_at DESC) AS position')])
            ->orderByRaw('pinned DESC, created_at DESC');

        if ($comment->reply_to) {
            $comments->where('reply_to', $comment->reply_to);
        } else {
            $comments->whereNull('reply_to');
        }

        $comments->where('commentable_type', $commentable->getMorphClass())->where('commentable_id', $commentable->id);

        $commentWithPage = Comment::from($comments, 'coms')->where('coms.id', $comment->id)->first();

        return ceil($commentWithPage?->position / $limit);
    }

    public static function subscribe(SubscribableInterface $sub)
    {
        $subs = $sub->subscriptions();
        $userId = Auth::user()->id;

        if ($subs->where('user_id', $userId)->exists()) {
            abort(409, 'Already subscribed');
        }

        $subs->create([
            'user_id' => $userId
        ]);
    }

    public static function unsubscribe(SubscribableInterface $sub)
    {
        $sub->subscriptions()->where('user_id', Auth::user()->id)->delete();
    }
}