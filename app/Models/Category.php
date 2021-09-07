<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'short_name',
        'desc',
        'downloads',
        'hidden',
        'grid',
        'disporder',
        'parent',
        'root',
        'approval_only',
        'banner',
        'thumbnail',
        'last_date',
        'webhook_url'
    ];

    public function game() : HasOne 
    {
        return $this->hasOne(Category::class, "id", 'game_id');
    }

    public function parent() : HasOne 
    {
        return $this->hasOne(Category::class, "id", 'parent_id');
    }
}
