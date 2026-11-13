<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HighlightedMod extends Model
{
    protected $guarded = [];

    public function mod()  {
        return $this->belongsTo(Mod::class);
    }

    public function game()  {
        return $this->belongsTo(Game::class);
    }
}
