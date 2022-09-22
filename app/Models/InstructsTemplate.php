<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Rennokki\QueryCache\Traits\QueryCacheable;

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
