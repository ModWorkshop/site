<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\ModMember
 *
 * @property int $id
 * @property int $level
 * @property int $mod_id
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereUserId($value)
 * @property bool $accepted
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereAccepted($value)
 * @mixin \Eloquent
 */
class ModMember extends Model
{
    use HasFactory;

    protected $guarded = [];

    
    public function getMorphClass(): string {
        return 'mod_member';
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function booted()
    {
        static::deleting(function(ModMember $request) {
            Notification::deleteRelated($request->user, 'membership_request');
        });
    }
}
