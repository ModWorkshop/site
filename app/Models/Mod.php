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
    protected $appends = ['submitter', 'game'];

    public function submitter() {
        return $this->hasOne(User::class, "id", 'submitter_uid')->latest();
    }

    public function game() {
        return $this->hasOne(Category::class, "id", 'game_id')->latest();
    }

    public function getSubmitterAttribute() {
        return $this->submitter()->first();
    }

    public function getGameAttribute() {
        return $this->game()->first();
    }
}
