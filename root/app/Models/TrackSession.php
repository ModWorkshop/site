<?php

namespace App\Models;

use Chelout\RelationshipEvents\Traits\HasRelationshipObservables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackSession extends Model
{
    const CREATED_AT = null;

    protected $guarded = [];



    use HasFactory;
}
