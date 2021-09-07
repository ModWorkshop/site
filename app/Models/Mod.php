<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    protected $appends = [];

    protected $with = ['tags', 'submitter', 'game', 'category'];
    
    public function scopeList($query)
    {
        return $query->without(['tags']);
    }

    public function submitter() : HasOne 
    {
        return $this->hasOne(User::class, "id", 'submitter_uid');
    }

    public function category() : HasOne 
    {
        return $this->hasOne(Category::class, "id", 'category_id');
    }

    public function game() : HasOne 
    {
        return $this->hasOne(Category::class, "id", 'game_id');
    }

    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
