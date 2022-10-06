<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supporter extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['user'];

    protected $casts = [
        'expire_date' => 'datetime',
    ];
    
    public function getMorphClass(): string {
        return 'supporter';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
