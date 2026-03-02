<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupporterPackage extends Model
{
    protected $guarded = [];

    public function getMorphClass(): string {
        return 'supporter_package';
    }
}
