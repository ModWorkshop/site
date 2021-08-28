<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    protected $with = ['submitter', 'game'];
    
    public function submitter() {
        return $this->hasOne(User::class, "id", 'submitter_uid');
    }

    public function game() {
        return $this->hasOne(Category::class, "id", 'game_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
