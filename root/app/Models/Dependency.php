<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Dependency
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $url
 * @property bool $offsite
 * @property int|null $mod_id
 * @property bool $optional
 * @property string $dependable_type
 * @property int $dependable_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Mod|null $mod
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency query()
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereDependableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereDependableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereOffsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereOptional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereUrl($value)
 * @property-read Model|\Eloquent $dependable
 * @mixin \Eloquent
 */
class Dependency extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['mod'];

    public function getMorphClass(): string {
        return 'dependency';
    }

    public function dependable(): MorphTo
    {
        return $this->morphTo();
    }

    public function mod(): BelongsTo
    {
        return $this->belongsTo(Mod::class);
    }
}
