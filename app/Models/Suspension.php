<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suspension extends Model
{
    //ඞ
    //I'm sorry
    protected $guarded = [];
    
    use HasFactory;

    public function getMorphClass(): string {
        return 'suspension';
    }
}
