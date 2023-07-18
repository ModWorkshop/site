<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * App\Models\RoleUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $role_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|RoleUser newModelQuery()
 * @method static Builder|RoleUser newQuery()
 * @method static Builder|RoleUser query()
 * @method static Builder|RoleUser whereCreatedAt($value)
 * @method static Builder|RoleUser whereId($value)
 * @method static Builder|RoleUser whereRoleId($value)
 * @method static Builder|RoleUser whereUpdatedAt($value)
 * @method static Builder|RoleUser whereUserId($value)
 * @mixin Eloquent
 */
class RoleUser extends Pivot
{
    use HasFactory;
}
