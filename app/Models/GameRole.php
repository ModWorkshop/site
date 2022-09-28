<?php

namespace App\Models;

use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Chelout\RelationshipEvents\Traits\HasRelationshipObservables;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Rennokki\QueryCache\Traits\QueryCacheable;

class GameRole extends Model
{
    use HasFactory, QueryCacheable, HasBelongsToManyEvents, HasRelationshipObservables;

    public $cacheFor = 60;
    public static $flushCacheOnUpdate = true;

    protected $with = [];
 
    protected $guarded = [];

    public function getMorphClass(): string {
        return 'game_role';
    }

    public function color(): Attribute
    {
        return Attribute::make(
            get: fn($value) =>  preg_replace('/\s+/', '', $value)
        );
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }
}
