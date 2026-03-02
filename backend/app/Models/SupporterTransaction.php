<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupporterTransaction extends Model
{
    protected $guarded = [];

    public function supporter() {
        return $this->belongsTo(Supporter::class);
    }

    public function user() {
        return $this->hasOne(User::class);
    }
}
