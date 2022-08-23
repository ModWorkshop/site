<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ForumCategory
 *
 * @property int $id
 * @property string $name
 * @property string $desc
 * @property int $forum_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereForumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ForumCategory extends Model
{
    use HasFactory, Filterable;

    protected $guarded = [];
}
