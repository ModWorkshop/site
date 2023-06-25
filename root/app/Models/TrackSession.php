<?php

namespace App\Models;

use Chelout\RelationshipEvents\Traits\HasRelationshipObservables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryCacheable;

class TrackSession extends Model
{
    const CREATED_AT = null;

    use QueryCacheable, HasRelationshipObservables;
    public $cacheFor = 60;

    protected $guarded = [];



    use HasFactory;
}
