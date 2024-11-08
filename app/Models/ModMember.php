<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * App\Models\ModMember
 *
 * @property int $id
 * @property int $level
 * @property int $mod_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ModMember newModelQuery()
 * @method static Builder|ModMember newQuery()
 * @method static Builder|ModMember query()
 * @method static Builder|ModMember whereCreatedAt($value)
 * @method static Builder|ModMember whereId($value)
 * @method static Builder|ModMember whereLevel($value)
 * @method static Builder|ModMember whereModId($value)
 * @method static Builder|ModMember whereUpdatedAt($value)
 * @method static Builder|ModMember whereUserId($value)
 * @property bool $accepted
 * @method static Builder|ModMember whereAccepted($value)
 * @property-read User|null $user
 * @mixin Eloquent
 */
class ModMember extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['user'];


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
