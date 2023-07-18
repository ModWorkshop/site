<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Report newModelQuery()
 * @method static Builder|Report newQuery()
 * @method static Builder|Report query()
 * @method static Builder|Report whereArchived($value)
 * @method static Builder|Report whereCreatedAt($value)
 * @method static Builder|Report whereData($value)
 * @method static Builder|Report whereId($value)
 * @method static Builder|Report whereReason($value)
 * @method static Builder|Report whereReportableId($value)
 * @method static Builder|Report whereReportableType($value)
 * @method static Builder|Report whereUpdatedAt($value)
 * @method static Builder|Report whereUserId($value)
 * @property string|null $name
 * @property int|null $game_id
 * @property-read Model|Eloquent $reportable
 * @property-read User $user
 * @method static Builder|Report whereGameId($value)
 * @method static Builder|Report whereName($value)
 * @property bool $locked
 * @method static Builder|Report whereLocked($value)
 * @property-read Game|null $game
 * @mixin Eloquent
 */
class Report extends Model
{
    use HasFactory;

    protected $casts = [
        'data' => 'array'
    ];

    protected $guarded = [];
    protected $with = ['user', 'reportable'];

    public function getMorphClass(): string {
        return 'report';
    }

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
