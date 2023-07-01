<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Mod|null $mod
 * @method static Builder|Dependency newModelQuery()
 * @method static Builder|Dependency newQuery()
 * @method static Builder|Dependency query()
 * @method static Builder|Dependency whereCreatedAt($value)
 * @method static Builder|Dependency whereDependableId($value)
 * @method static Builder|Dependency whereDependableType($value)
 * @method static Builder|Dependency whereId($value)
 * @method static Builder|Dependency whereModId($value)
 * @method static Builder|Dependency whereName($value)
 * @method static Builder|Dependency whereOffsite($value)
 * @method static Builder|Dependency whereOptional($value)
 * @method static Builder|Dependency whereOrder($value)
 * @method static Builder|Dependency whereUpdatedAt($value)
 * @method static Builder|Dependency whereUrl($value)
 * @property-read Model|Eloquent $dependable
 * @mixin Eloquent
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
        return $this->belongsTo(Mod::class)->without(['dependencies']);
    }
}
