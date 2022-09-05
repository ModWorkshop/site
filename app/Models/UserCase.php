<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserCase
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $mod_user_id
 * @property bool $warning
 * @property string $reason
 * @property string|null $expire_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereModUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereWarning($value)
 * @mixin \Eloquent
 */
class UserCase extends Model
{
    protected $guarded = [];

    use HasFactory;

    public function getMorphClass(): string {
        return 'user_case';
    }
}
