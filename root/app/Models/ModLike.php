<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\ModLike
 *
 * @property int $id
 * @property int $mod_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ModLike newModelQuery()
 * @method static Builder|ModLike newQuery()
 * @method static Builder|ModLike query()
 * @method static Builder|ModLike whereCreatedAt($value)
 * @method static Builder|ModLike whereId($value)
 * @method static Builder|ModLike whereModId($value)
 * @method static Builder|ModLike whereUpdatedAt($value)
 * @method static Builder|ModLike whereUserId($value)
 * @mixin Eloquent
 */
class ModLike extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getMorphClass(): string {
        return 'mod_like';
    }
}
