<?php

namespace App\Models;

use App\Services\ModService;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Game extends Model
{
    use HasFactory, Filterable;
    protected $guarded = [];

    protected $hidden = [];
    
    protected $with = [];

    public function getBreadcrumbAttribute()
    {
        return ModService::categoryCrumb($this);
    }
}