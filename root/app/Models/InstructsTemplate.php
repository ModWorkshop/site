<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\InstructsTemplate
 *
 * @property int $id
 * @property string $name
 * @property string $instructions
 * @property bool $localized
 * @property int $game_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Dependency[] $dependencies
 * @property-read int|null $dependencies_count
 * @method static Builder|InstructsTemplate newModelQuery()
 * @method static Builder|InstructsTemplate newQuery()
 * @method static Builder|InstructsTemplate query()
 * @method static Builder|InstructsTemplate whereCreatedAt($value)
 * @method static Builder|InstructsTemplate whereGameId($value)
 * @method static Builder|InstructsTemplate whereId($value)
 * @method static Builder|InstructsTemplate whereInstructions($value)
 * @method static Builder|InstructsTemplate whereLocalized($value)
 * @method static Builder|InstructsTemplate whereName($value)
 * @method static Builder|InstructsTemplate whereUpdatedAt($value)
 * @property-read Game $game
 * @mixin Eloquent
 */
class InstructsTemplate extends Model
{
    use HasFactory;

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

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
