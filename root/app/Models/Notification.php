<?php

namespace App\Models;

use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Notification
 *
 * @property int $id
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property string $context_type
 * @property int $context_id
 * @property string $type
 * @property bool $seen
 * @property mixed $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Notification newModelQuery()
 * @method static Builder|Notification newQuery()
 * @method static Builder|Notification query()
 * @method static Builder|Notification whereContextId($value)
 * @method static Builder|Notification whereContextType($value)
 * @method static Builder|Notification whereCreatedAt($value)
 * @method static Builder|Notification whereData($value)
 * @method static Builder|Notification whereId($value)
 * @method static Builder|Notification whereNotifiableId($value)
 * @method static Builder|Notification whereNotifiableType($value)
 * @method static Builder|Notification whereSeen($value)
 * @method static Builder|Notification whereType($value)
 * @method static Builder|Notification whereUpdatedAt($value)
 * @property-read Model|Eloquent $context
 * @property-read Model|Eloquent $notifiable
 * @property-read User $user
 * @property int $user_id
 * @method static Builder|Notification whereUserId($value)
 * @property int|null $from_user_id
 * @property-read User|null $fromUser
 * @method static Builder|Notification whereFromUserId($value)
 * @mixin Eloquent
 */
class Notification extends Model
{
    use HasFactory;

    protected $with = ['user', 'notifiable', 'context', 'fromUser'];

    protected $casts = [
        'data' => 'array'
    ];

    public function getMorphClass(): string {
        return 'notification';
    }

    public static function send(string $type, User $user, Model $notifiable = null, Model $context = null, bool $hideSender = false, array $data=null)
    {
        $authUser = Auth::user();

        if ($authUser->id === $user->id) {
            return null;
        }

        $notif = new Notification([
            'type' => $type,
            'from_user_id' => !$hideSender ? $authUser->id : null,
            'data' => $data
        ]);

        if (isset($notifiable)) {
            $notif->notifiable()->associate($notifiable);
        }

        if (isset($context)) {
            $notif->context()->associate($context);
        }

        if (isset($user)) {
            $notif->user_id = $user->id;
        }

        $notif->save();

        return $notif;
    }

    /**
     * Handy way of deleting related notifications to notifiable or context models
     * For example: User deletes comment -> Related notifications are deleted -> User is not confused by dead notifications
     *
     * @param Model|null $notifiable
     * @param Model|null $context
     * @return void
     */
    public static function deleteRelated(Model $model, string $notifType=null)
    {
        $q = Notification::query();
        $id = $model->id;
        $type = $model->getMorphClass();

        if (isset($notifType)) {
            $q->where('type', $notifType);
        }

        $q->where(fn($q) => $q->where('notifiable_type', $type)->where('notifiable_id', $id));
        $q->orWhere(fn($q) => $q->where('context_type', $type)->where('context_id', $id));
        $q->delete();
    }

    protected $guarded = [];

    /**
     * The thing that we got notified by. We are subscribed to a mod discussion, our comment.
     * Essentially, what *triggered* the notification.
     */
    public function notifiable()
    {
        return $this->morphTo()->morphWith([
            Comment::class => 'commentable'
        ]);
    }

    /**
     * Context for the notification, if we got a comment in a mod we subscribed to, we need to know what comment it is.
     * In some cases notifiable is enoough. For example a mod got updated, we don't need anything else.
     */
    public function context()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromUser()
    {
        return $this->hasOne(User::class, 'id', 'from_user_id');
    }
}
