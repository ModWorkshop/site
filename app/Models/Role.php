<?php

namespace App\Models;

use Auth;
use Eloquent;
use Exception;
use Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property string $tag
 * @property string $desc
 * @property string $color
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Permission[] $permissions
 * @property-read int|null $permissions_count
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role query()
 * @method static Builder|Role whereColor($value)
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereDesc($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereOrder($value)
 * @method static Builder|Role whereTag($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @property bool $is_vanity
 * @method static Builder|Role whereIsVanity($value)
 * @property bool $self_assignable
 * @method static Builder|Role whereSelfAssignable($value)
 * @property-read mixed $cached_permissions
 * @mixin Eloquent
 */
class Role extends Model
{
    use HasFactory;

    public function cacheKey()
    {
        return sprintf("%s/%s-%s", $this->getTable(), $this->getKey(), $this->updated_at?->timestamp ?? $this->id);
    }

    protected $with = [];

    protected $guarded = [
        'is_vanity'
    ];

    public function getMorphClass(): string {
        return 'role';
    }

    public function permissions() : BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }
    
    public function permit(string $permission) {
        $this->permissions()->attach(Permission::whereName($permission)->first()->id);
    }

    public function color(): Attribute
    {
        return Attribute::make(fn($value) =>  preg_replace('/\s+/', '', $value));
    }

    public function cachedPermissions(): Attribute {
        return Attribute::make(
            fn() => Cache::remember($this->cacheKey().':permission', 60, fn() => $this->permissions)
        );
    }

    /**
     * Returns whether or not the role can be edited by the user.
     */
    public function canBeEdited()
    {
        $me = Auth::user();

        //Not signed in? BTFU
        if (!isset($me)) {
            return false;
        }

        $myHighestOrder = $me->highestRoleOrder;

        if ($me->id === 1) {
            return true;
        } else {
            return $me->hasPermission('manage-roles') && $myHighestOrder > $this->order;
        }
    }

    /**
     * Syncs the permissions of the role with the given $perms
     * If you don't have the permission you cannot give or deny them.
     */
    public function syncPerms(array $perms)
    {
        $me = Auth::user();
        foreach ($perms as $perm) {
            if (!$me->hasPermission($perm)) {
                abort(403, "You can't assign permissions you don't have yourself to roles.");
            }
        }

        foreach ($this->permissions as $perm) {
            if (!isset($perms[$perm->name]) && !$me->hasPermission($perm)) {
                abort(403, "You can't deny permissions you don't have yourself to roles.");
            }
        }

        $this->permissions()->sync(Permission::whereIn('name', $perms)->get());
    }
}
