<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Rennokki\QueryCache\Traits\QueryCacheable;

/**
 * App\Models\InstructsTemplate
 *
 * @property int $id
 * @property string $name
 * @property string $instructions
 * @property bool $localized
 * @property int $game_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Dependency[] $dependencies
 * @property-read int|null $dependencies_count
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereLocalized($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InstructsTemplate extends Model
{
    use HasFactory, QueryCacheable;

    public $cacheFor = 120;
    public static $flushCacheOnUpdate = true;

    protected $with = ['dependencies'];
    protected $guarded = [];
    
    public function getMorphClass(): string {
        return 'instructs_template';
    }

    /**
     * Depenencies of the mod for the instructions
     *
     * @return HasMany
     */
    public function dependencies() : MorphMany
    {
        return $this->morphMany(Dependency::class, 'dependable');
    }
}
