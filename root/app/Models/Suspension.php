<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Suspension
 *
 * @property int $id
 * @property int $mod_id
 * @property int|null $mod_user_id
 * @property string $reason
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Suspension newModelQuery()
 * @method static Builder|Suspension newQuery()
 * @method static Builder|Suspension query()
 * @method static Builder|Suspension whereCreatedAt($value)
 * @method static Builder|Suspension whereId($value)
 * @method static Builder|Suspension whereModId($value)
 * @method static Builder|Suspension whereModUserId($value)
 * @method static Builder|Suspension whereReason($value)
 * @method static Builder|Suspension whereStatus($value)
 * @method static Builder|Suspension whereUpdatedAt($value)
 * @property-read Mod $mod
 * @mixin Eloquent
 */
class Suspension extends Model
{
    //ඞ
    //I'm sorry
    protected $guarded = [];

    use HasFactory;

    public function getMorphClass(): string {
        return 'suspension';
    }

    public function mod(): BelongsTo
    {
        return $this->belongsTo(Mod::class);
    }
}
