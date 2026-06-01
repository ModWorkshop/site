<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IgnoredCategory whereUserId($value)
 * @mixin \Eloquent
 */
class IgnoredCategory extends Model
{
    protected $guarded = [];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMorphClass(): string {
        return 'ignored_category';
    }
}
