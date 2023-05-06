<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ModLike
 *
 * @property int $id
 * @property int $mod_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike whereUserId($value)
 * @mixin \Eloquent
 */
class ModLike extends Model
{
    use HasFactory;

    public function getMorphClass(): string {
        return 'mod_like';
    }
}
