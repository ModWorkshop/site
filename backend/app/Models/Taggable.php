<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\Taggable
 *
 * @property int $id
 * @property int $tag_id
 * @property string $taggable_type
 * @property int $taggable_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Taggable newModelQuery()
 * @method static Builder|Taggable newQuery()
 * @method static Builder|Taggable query()
 * @method static Builder|Taggable whereCreatedAt($value)
 * @method static Builder|Taggable whereId($value)
 * @method static Builder|Taggable whereTagId($value)
 * @method static Builder|Taggable whereTaggableId($value)
 * @method static Builder|Taggable whereTaggableType($value)
 * @method static Builder|Taggable whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Taggable extends Model
{
    use HasFactory;
}
