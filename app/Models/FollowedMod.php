<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowedMod extends Model
{
    use HasFactory, Filterable;

    protected $guarded = [];
}
