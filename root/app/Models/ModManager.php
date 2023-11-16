<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModManager extends Model
{
    protected $guarded = [];

    use HasFactory;

    function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    function games() {
        return $this->belongsToMany(Game::class);
    }
}
