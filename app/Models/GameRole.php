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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\GameRole
 *
 * @property int $id
 * @property string $name
 * @property string $tag
 * @property string $desc
 * @property string $color
 * @property int $game_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $is_vanity
 * @property-read Game $game
 * @property-read Collection|Permission[] $permissions
 * @property-read int|null $permissions_count
 * @method static Builder|GameRole newModelQuery()
 * @method static Builder|GameRole newQuery()
 * @method static Builder|GameRole query()
 * @method static Builder|GameRole whereColor($value)
 * @method static Builder|GameRole whereCreatedAt($value)
 * @method static Builder|GameRole whereDesc($value)
 * @method static Builder|GameRole whereGameId($value)
 * @method static Builder|GameRole whereId($value)
 * @method static Builder|GameRole whereIsVanity($value)
 * @method static Builder|GameRole whereName($value)
 * @method static Builder|GameRole whereOrder($value)
 * @method static Builder|GameRole whereTag($value)
 * @method static Builder|GameRole whereUpdatedAt($value)
 * @property bool $self_assignable
 * @method static Builder|GameRole whereSelfAssignable($value)
 * @property-read mixed $cached_permissions
 * @mixin Eloquent
 */
class GameRole extends Model
{
    use HasFactory;

    protected $with = [];

    protected $hidden = ['game'];

    protected $guarded = [];

    public function cacheKey()
    {
        return sprintf("%s/%s-%s", $this->getTable(), $this->getKey(), $this->updated_at?->timestamp ?? $this->id);
    }

    public function permit(string $permission) {
        $this->withSecureConstraints(fn() => $this->permissions()->attach(Permission::whereName($permission)->first()->id));
    }

    public function getMorphClass(): string {
        return 'game_role';
    }

    public function color(): Attribute
    {
        return Attribute::make(
            get: fn($value) =>  preg_replace('/\s+/', '', $value)
        );
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
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

        $myHighestOrder = $me->getGameHighestOrder($this->game->id);

        if ($me->id === 1) {
            return true;
        } else if ($me->hasPermission('manage-roles')) {
            //A user that can manage roles globally, can essentially edit any game role.
            return true;
        } else {
            return $me->hasPermission('manage-roles', $this->game) && $myHighestOrder > $this->order;
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
            if (!$me->hasPermission($perm, $this->game)) {
                abort(403, "You can't assign permissions you don't have yourself to game roles.");
            }
        }

        foreach ($this->permissions as $perm) {
            if (!isset($perms[$perm->name]) && !$me->hasPermission($perm, $this->game)) {
                abort(403, "You can't deny permissions you don't have yourself to game roles.");
            }
        }

        $this->withSecureConstraints(fn() => $this->permissions()->sync(Permission::whereIn('name', $perms)->get()));
    }
}
