<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dependency extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['mod'];

    public function getMorphClass(): string {
        return 'dependency';
    }

    public function mod(): BelongsTo
    {
        return $this->belongsTo(Mod::class);
    }
}
