<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Subscription
 *
 * @property int $id
 * @property int $user_id
 * @property string $subscribable_type
 * @property int $subscribable_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Subscription newModelQuery()
 * @method static Builder|Subscription newQuery()
 * @method static Builder|Subscription query()
 * @method static Builder|Subscription whereCreatedAt($value)
 * @method static Builder|Subscription whereId($value)
 * @method static Builder|Subscription whereSubscribableId($value)
 * @method static Builder|Subscription whereSubscribableType($value)
 * @method static Builder|Subscription whereUpdatedAt($value)
 * @method static Builder|Subscription whereUserId($value)
 * @property-read User $user
 * @mixin Eloquent
 */
class Subscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getMorphClass(): string {
        return 'subscription';
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
