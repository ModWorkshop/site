<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowedGame extends Model
{
    use HasFactory, Filterable;
    
    protected $guarded = [];
}
