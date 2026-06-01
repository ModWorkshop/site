<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $mod_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredMod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredMod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredMod query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredMod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredMod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredMod whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredMod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredMod whereUserId($value)
 * @mixin \Eloquent
 */
class IgnoredMod extends Model
{
    protected $guarded = [];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMorphClass(): string {
        return 'ignored_mod';
    }
}
