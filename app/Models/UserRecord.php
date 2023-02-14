<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRecord extends Model
{
    protected $guarded = [];

    protected $casts = [
        'social_logins' => 'array'
    ];

    use HasFactory;
}
