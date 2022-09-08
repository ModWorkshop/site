<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Rennokki\QueryCache\Traits\QueryCacheable;

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereContextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereContextType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereNotifiableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereNotifiableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Model|\Eloquent $context
 * @property-read Model|\Eloquent $notifiable
 * @property-read \App\Models\User $user
 * @property int $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUserId($value)
 */
class Notification extends Model
{
    use HasFactory, QueryCacheable;

    public $cacheFor = 10;
    public static $flushCacheOnUpdate = true;

    protected $with = ['user', 'notifiable', 'context'];

    public function getMorphClass(): string {
        return 'notification';
    }

    public static function send(string $type, User $user, Model $notifiable = null, Model $context = null)
    {
        $notif = new Notification([
            'type' => $type
        ]);

        if (isset($notifiable)) {
            $notif->notifiable()->associate($notifiable);
        }
        
        if (isset($context)) {
            $notif->context()->associate($context);
        }

        if (isset($user)) {
            $notif->user()->associate($user);
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
            'comment' => 'commentable'
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
}
