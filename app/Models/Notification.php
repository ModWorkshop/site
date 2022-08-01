<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    use HasFactory, Filterable;

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
    
    protected $guarded = [];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function context()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
