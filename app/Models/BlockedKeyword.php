<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BlockedKeyword
 *
 * @property int $id
 * @property int $user_id
 * @property string $keyword
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedKeyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedKeyword newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedKeyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedKeyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedKeyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedKeyword whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedKeyword whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedKeyword whereUserId($value)
 * @mixin \Eloquent
 */
class BlockedKeyword extends Model
{
    use HasFactory;
}
