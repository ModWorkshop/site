<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Suspension
 *
 * @property int $id
 * @property int $mod_id
 * @property int|null $mod_user_id
 * @property string $reason
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension query()
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereModUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Mod $mod
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
