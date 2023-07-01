<?php

namespace App\Models;

use Auth;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Chelout\RelationshipEvents\Traits\HasRelationshipObservables;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_vanity
 * @property-read \App\Models\Game $game
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereIsVanity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereUpdatedAt($value)
 * @property bool $self_assignable
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereSelfAssignable($value)
 * @mixin \Eloquent
 */
class GameRole extends Model
{
    use HasFactory;

    protected $with = [];

    protected $guarded = [];

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

        $this->permissions()->sync(Permission::whereIn('name', $perms)->get());
    }
}
