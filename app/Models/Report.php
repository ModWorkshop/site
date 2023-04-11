<?php

namespace App\Models;

use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rennokki\QueryCache\Traits\QueryCacheable;

/**
 * App\Models\Report
 *
 * @property int $id
 * @property mixed $data
 * @property int $user_id
 * @property string $reason
 * @property bool $archived
 * @property string $reportable_type
 * @property int $reportable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereReportableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereReportableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUserId($value)
 * @property string|null $name
 * @property int|null $game_id
 * @property-read Model|\Eloquent $reportable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereName($value)
 * @property bool $locked
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereLocked($value)
 * @property-read \App\Models\Game|null $game
 * @mixin \Eloquent
 */
class Report extends Model
{
    use HasFactory;
    use QueryCacheable;

    public $cacheFor = 1200;

    protected $casts = [
        'data' => 'array'
    ];

    protected $guarded = [];
    protected $with = ['user', 'reportable'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function reportable()
    {
        return $this->morphTo();
    }
}
