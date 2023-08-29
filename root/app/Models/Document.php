<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Document
 *
 * @property int $id
 * @property string $name
 * @property string $url_name
 * @property string $desc
 * @property int $game_id
 * @property int $last_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Document newModelQuery()
 * @method static Builder|Document newQuery()
 * @method static Builder|Document query()
 * @method static Builder|Document whereCreatedAt($value)
 * @method static Builder|Document whereDesc($value)
 * @method static Builder|Document whereGameId($value)
 * @method static Builder|Document whereId($value)
 * @method static Builder|Document whereLastUserId($value)
 * @method static Builder|Document whereName($value)
 * @method static Builder|Document whereUpdatedAt($value)
 * @method static Builder|Document whereUrlName($value)
 * @property-read User $lastUser
 * @property-read Game|null $game
 * @mixin Eloquent
 */
class Document extends Model
{
    use HasFactory;

    protected $with = ['lastUser'];
    protected $guarded = [];

    public function lastUser()
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function getMorphClass(): string {
        return 'document';
    }
}
