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

    public function submitter() {
        return $this->hasOne(User::class, "id", 'submitter_uid')->latest();
    }

    protected $appends = ['submitter'];

    public function getSubmitterAttribute() {
        return $this->submitter()->first();
    }
}
