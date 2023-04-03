<?php

namespace App\Models;

use Auth;
use Chelout\RelationshipEvents\BelongsToMany;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Chelout\RelationshipEvents\Traits\HasRelationshipObservables;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property string $tag
 * @property string $desc
 * @property string $color
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property bool $is_vanity
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereIsVanity($value)
 */
class Role extends Model
{
    use HasFactory, QueryCacheable, HasBelongsToManyEvents, HasRelationshipObservables;

    public $cacheFor = 60;
    public static $flushCacheOnUpdate = true;

    protected $with = [];
 
    protected $guarded = [
        'is_vanity'
    ];

    public function getMorphClass(): string {
        return 'role';
    }

    public function color() : Attribute
    {
        return Attribute::make(
            get: fn($value) =>  preg_replace('/\s+/', '', $value)
        );
    }

    public function permissions() : BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
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
                throw new Exception("You can't assign permissions you don't have yourself to roles.");
            }
        }

        foreach ($this->permissions as $perm) {
            if (!isset($perms[$perm->name]) && !$me->hasPermission($perm)) {
                throw new Exception("You can't deny permissions you don't have yourself to roles.");
            }
        }

        $this->permissions()->sync(Permission::whereIn('name', $perms)->get());
    }
}
