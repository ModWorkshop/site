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
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentService {
    /**
     * Display a listing of the resource.
     *
     * @return Response
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

        if (isset($commentable->game)) {
            APIService::setCurrentGame($commentable->game);
        }

        $comments = $query->queryGet($request->validated(), function($query, array $val) use ($options, $replies, $commentable) {
            if ($options['include_last_replies'] ?? true) {
                $query->with('lastReplies');
            }
            $query->withCount('replies');
            $query->orderByRaw($options['orderBy'] ?? 'pinned DESC, created_at DESC');
            if (!isset($replies) && !($options['include_replies'] ?? false)) {
                $query->whereNull('reply_to');
            }
            if ($options['commentable_is_user'] ?? false) {
                $query->whereUserId($commentable->id);
            } else {
                $query->whereMorphedTo('commentable', $commentable);
            }
        });

        return CommentResource::collectionResponse($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public static function store(Request $request, Model $commentable, array $extraSet=null)
    {
        $val = $request->validate([
            'content' => 'string|required|min_strict:2|max:5000',
            'mentions' => 'array',
            'mentions.*' => 'string',
            'reply_to' => 'integer|nullable|min:1|exists:comments,id,reply_to,NULL'
        ]);

        $user = Auth::user();

        // Check if the message is spammy, do not run on trusted users
        $trustLevel = $user->getTrustLevel();
        if ($trustLevel < 12) {
            if (APIService::checkSpamContent($val['content'])) {
                abort(422, 'Comment contains spam content!');
            }
        }

        //Make sure to limit this to 20 users and not include ourselves!
        $mentions = Arr::pull($val, 'mentions');
        $uniqueNames = [];
        $mentionedUsers = [];

        if (isset($mentions)) {
            $uniqueNames = array_slice($mentions, 0, 10);
            $mentionedUsers = User::whereIn(DB::raw('LOWER(unique_name)'), $uniqueNames)->limit(10)->get();
        }

        $val['user_id'] = $user->id;

        $isReply = isset($val['reply_to']);
        $replyToComment = $isReply ? Comment::find($val['reply_to']) : null;

        if ($replyToComment) {
            //Make sure that whatever we are trying to reply to is a comment that is on the same commentable object (mod/thread).
            if ($replyToComment->commentable_type !== $commentable->getMorphClass() || $replyToComment->commentable_id !== $commentable->id) {
                abort(409, "Invalid comment to reply to");
            }

            //Make sure we are not blocked
            if (!$user->hasPermission('manage-discussions') && $replyToComment->user->blockedMe) {
                abort(401);
            }
        }

        if (isset($extraSet)) {
            $val = [...$val, ...$extraSet];
        }

        $val['parser_version'] = 2;

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
        // Send subscription notification, avoid sending notification again to who mentioned users
        //TODO: move to a job rather than do it in the same request
        $subs = $notifiable->subscriptions()->whereNotIn('user_id', $mentionedIds)->get()->unique('user_id');
        foreach ($subs as $sub) {
            Notification::send(
                notifiable: $notifiable,
                context: $comment,
                user: $sub->user,
                type: "sub_{$sub->subscribable_type}"
            );
        }

        $comment->load(['mentions', 'subscribed']);

        return new CommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  Mod  $mod
     * @param Comment $comment
     * @return Response
     */
    public static function update(Request $request, Comment $comment)
    {
        $user = $request->user();
        $val = $request->validate([
            'mentions' => 'array',
            'content' => 'string|required_without:pinned|min:3|max:5000',
        ]);

        $mentions = Arr::pull($val, 'mentions');
        $uniqueNames = [];
        $mentionedUsers = [];
        $mentionedIds = [];

        //While we allow mod members to pin comments, we should NEVER allow them to edit them!
        if ((isset($val['content']) || isset($mentions)) && (!$user->hasPermission('manage-discussions', $comment->commentable->game) && $comment->user->id !== $user->id)) {
            abort(403, 'You cannot edit the comment!');
        }

        if (isset($mentions)) {
            $uniqueNames = array_slice($mentions, 0, 10);
            $mentionedUsers = User::whereIn(DB::raw('LOWER(unique_name)'), $uniqueNames)->limit(10)->without('roles')->get('id');
        }

        if (isset($mentions)) {
            foreach ($mentionedUsers as $mentionedUser) {
                $mentionedIds[] = $mentionedUser->id;
            }

            //Update the mentions, but avoid sending any notifications to avoid spam.
            $comment->mentions()->sync($mentionedIds);
        }

        $val['parser_version'] = 2;

        $comment->update($val);

        return new CommentResource($comment);
    }

    /**
     * Set the pinned state of a comment
     */
    public static function setPinned(Request $request, Comment $comment) {
        $val = $request->validate([
            'status' => 'boolean'
        ]);

        if ($comment->reply_to && isset($val['status'])) {
            abort(403, 'Only regular comments can be pinned!');
        }

        $comment->timestamps = false;

        $comment->update([
            'pinned' => $val['status']
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Comment  $comment
     */
    public static function destroy(Comment $comment)
    {
        $comment->delete();
    }

    /**
     * Finds the page of the comment
     */
    public static function page(Request $request, Comment $comment)
    {
        $limit = $request->input('limit', 20);

        $order = 'DESC';
        if (!isset($comment->reply_to) && !$comment->commentable_type == 'mod') {
            $order = 'ASC';
        }

        $comments = DB::table('comments')
            ->select([
                'id',
                'commentable_id',
                'commentable_type',
                'reply_to',
                DB::raw("row_number() over(ORDER BY pinned DESC, created_at {$order}) AS position")
            ])
        ->orderByRaw("pinned DESC, created_at {$order}");

        if ($comment->reply_to) {
            $comments->where('reply_to', $comment->reply_to);
        } else {
            $comments->whereNull('reply_to');
        }

        $comments->where('commentable_type', $comment->commentable->getMorphClass())->where('commentable_id', $comment->commentable->id);

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
