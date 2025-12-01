<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameHiddenTag extends Model
{
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function games() {
        return $this->belongsToMany(Game::class);
    }

    public function getMorphClass(): string {
        return 'game_hidden_tag';
    }
}
