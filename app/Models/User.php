<?php

namespace App\Models;

use Arr;
use Auth;
use Carbon\Carbon;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Chelout\RelationshipEvents\Traits\HasRelationshipObservables;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Log;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Storage;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $avatar
 * @property-read mixed $permissions
 * @property-read mixed $role_names
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereAvatar($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User withPermissions()
 * @mixin \Eloquent
 * @property-read \App\Models\UserExtra|null $extra
 * @property string $custom_color
 * @method static Builder|User whereCustomColor($value)
 * @property string|null $unique_name
 * @method static Builder|User whereUniqueName($value)
 * @property-read \App\Models\Ban|null $lastBan
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BlockedTag[] $blockedTags
 * @property-read int|null $blocked_tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $blockedUsers
 * @property-read int|null $blocked_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $fullyBlockedUsers
 * @property-read int|null $fully_blocked_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mod[] $mods
 * @property-read int|null $mods_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Game[] $followedGames
 * @property-read int|null $followed_games_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mod[] $followedMods
 * @property-read int|null $followed_mods_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $followedUsers
 * @property-read int|null $followed_users_count
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use QueryCacheable, HasBelongsToManyEvents, HasRelationshipObservables;

    public $cacheFor = 10;
    public static $flushCacheOnUpdate = true;

    public static $membersRole = null;
    
    // Always return roles for users
    protected $with = ['roles', 'lastBan'];
    private $permissions  = [];
    private $roleNames = [];

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'unique_name' => $this->unique_name,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'custom_color',
        'unique_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'roles',
        'password',
        'remember_token',
        'email',
        'email_verified',
        'email_verified_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    private $gotPerms = false;
    private $gotRoles = false;

    public function blockedByMe()
    {
        return $this->hasOneThrough(User::class, BlockedUser::class, 'block_user_id', 'id', 'id', 'block_user_id')->where('blocked_users.user_id', Auth::user()->id)->limit(1);
    }

    public function blockedMe()
    {
        return $this->hasOneThrough(User::class, BlockedUser::class, 'user_id', 'id', 'id', 'user_id')->where('blocked_users.block_user_id', Auth::user()->id)->limit(1);
    }

    /**
     * Returns the follow model (if exists) of the user for the authenticated user
     */
    public function followed() : HasOne
    {
        return $this->hasOne(FollowedUser::class, 'follow_user_id')->where('user_id', Auth::user()->id);
    }

    public function followingGames() : HasMany
    {
        return $this->hasMany(FollowedGame::class);
    }

    public function followingMods() : HasMany
    {
        return $this->hasMany(FollowedMod::class);
    }

    public function followingUsers() : HasMany
    {
        return $this->hasMany(FollowedUser::class);
    }

    public function getMorphClass(): string {
        return 'user';
    }

    protected static function booted()
    {
        self::created(fn(User $user) => $user->extra()->create());

        self::deleting(function(User $user) {
            if (isset($user->avatar) && !str_contains($user->avatar, 'http')) {
                Storage::disk('public')->delete('users/avatars/'.$user->avatar);
            }

            $banner = $user->extra->banner;
            if (isset($banner)) {
                Storage::disk('public')->delete('users/banners/'.$banner);
            }

            foreach ($user->mods as $mod) {
                $mod->delete();
            }
        });
    }

    /**
     * The users the user has blocked fully. No mods no communication
     */
    public function fullyBlockedUsers() : BelongsToMany
    {
        return $this->belongsToMany(User::class, BlockedUser::class, null, 'block_user_id')->where('silent', false);
    }

    /**
     * The users the user "soft blocked" i.e. hid their mods (also includes fully blocked)
     * 
     */
    public function blockedUsers() : BelongsToMany
    {
        return $this->belongsToMany(User::class, BlockedUser::class, null, 'block_user_id')->withPivot('silent');
    }

    /**
     * The user's blocked tags. Not loaded normally
     */
    public function blockedTags() : HasMany
    {
        return $this->hasMany(BlockedTag::class);        
    }

    public function mods() : HasMany
    {
        return $this->hasMany(Mod::class);
    }

    public function extra() : HasOne
    {
        return $this->hasOne(UserExtra::class);
    }

    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(Role::class)->orderBy('order');
    }

    public function getRoleNames()
    {
        if ($this->gotRoles) {
            return $this->roleNames;
        }

        self::$membersRole ??= Role::with('permissions')->find(1);
        $roles = $this->roles;
        if (!$roles->contains(self::$membersRole)) {
            $roles[] = self::$membersRole;
        }

        $rolesNames = [];

        foreach ($roles as $role) {
            $rolesNames[] = $role->name;
        }

        $this->gotRoles = true;

        return $rolesNames;
    }

    public function getPermissions(bool $forceLoad=false)
    {
        if ($this->gotPerms) {
            return $this->permissions;
        }

        $this->gotPerms = true;

        self::$membersRole ??= Role::with('permissions')->find(1);
        $roles = $this->roles;
        if (!$roles->contains(self::$membersRole)) {
            $roles[] = self::$membersRole;
        }

        /**
         * THIS IS TEMPORARY
         * the system should not allow for "permission racing", basically if we deny permission once, it should NOT give it again.
         * Same thing goes with granting, if we give a permission (aside members role), it should not be denied.
         * The purpose of the allow variable is only take away permissions when necessary and not have to do give - take - give.
         * Only give and take with the first give being pretty much the members role.
         */

        foreach ($roles as $role) {
            if ($forceLoad || $role->relationLoaded('permissions')) {
                foreach ($role->permissions as $perm) {
                    $slug = $perm->slug;
                    if ($perm->pivot->allow) {
                        if (!$this->hasPermission($perm->slug)) {
                            $this->grantPermission($slug);
                        }
                    } else {
                        if ($this->hasPermission($perm->slug)) {
                            $this->denyPermission($slug);
                        }
                    }
                }
           } else {
              return null;
           }
        }

        return $this->permissions;
    }

    public function hasRole(int $id)
    {
        $ids = Arr::pluck($this->roles, 'id');
        return in_array($id, $ids);
    }

    /**
     * Checks whether the user has a certain permission.
     * First it checks if the user has admin permission, admin bypasses all permissions at the moment.
     * Guests are permission-less meaning that if we want to let them see something, that thing must have no needed permissions.
     * A banned user always returns false and makes the user act like a guest, sort of. The frontend should make it clearer.
     *
     * @param string $toWhat
     * @return boolean
     */
    function hasPermission(string $toWhat) {
        if ($toWhat !== 'admin' && $this->hasPermission('admin')) {
            return true;
        }

        if ($this->lastBan) {
            return false;
        }

        $permissions = $this->getPermissions(true);
        return isset($permissions[$toWhat]) && $permissions[$toWhat] === true;
    }

    /**
     * Takes away a permission from a user
     *
     * @param string $toWhat
     * @return void
     */
    private function denyPermission(string $toWhat)
    {
        $this->permissions[$toWhat] = false;
    }

    /**
     * Grants permission to a user
     *
     * @param string $toWhat
     * @return void
     */
    private function grantPermission(string $toWhat)
    {
        $this->permissions[$toWhat] = true;
    }

    protected $appends = ['role_names'];

    public function getRoleNamesAttribute() {
        return $this->getRoleNames();
    }

    public function getPermissionsAttribute() {
        return $this->getPermissions();
    }

    public function lastBan()
    {
        return $this->hasOne(Ban::class)->where('expire_date', '>', Carbon::now());
    }

    /**
     * Returns the "highest" order role
     * Highest goes from lowest to highest. So 0 is the highest.
     * -1, or Members, is returned as null, it could be seen as "infinite" as it will awlays be the lowest.
     * @return int|null
     */
    public function getHighestRoleOrder()
    {
        $highest = null;
        foreach ($this->roles as $role) {
            if ($role->id != 1 && (!$highest || $role->order < $highest->order)) {
                $highest = $role;
            }
        }

        if (!isset($highest)) {
            return null;
        }

        $order = $highest?->order ?? 9999;

        //Special case for the Super Admin
        if ($this->id === 1) {
            $order--;
        }

        return $order;
    }

    /**
     * Returns whether or not the user can be edited by the "other" user.
     * The other user defaults to the currently authenticated user
     *
     * @param User|null $other
     * @return boolean
     */
    public function canBeEdited(User $other=null)
    {
        $other ??= Auth::user();

        //Not signed in? BTFU
        if (!isset($other)) {
            return false;
        }

        $otherHighestOrder = $other->getHighestRoleOrder();
        $highestOrder = $this->getHighestRoleOrder();

        //If we try to edit ourselves then return true
        if ($other->id === $this->id) {
            return true;
        }

        //Make sure we have an order
        if ($other->hasPermission('edit-user') && $otherHighestOrder) {
            //If the user has only Members it's safe to assume we can edit them
            //If they have an order then let's make sure the other's is higher in order.
            if (!$highestOrder || $otherHighestOrder < $highestOrder) {
                return true;
            }
        }

        return $other->id === $this->id || ($other->hasPermission('edit-user') && $otherHighestOrder < $highestOrder);
    }

    /**
     * A safe way of setting and saving roles
     * The function makes sure that whoever gives the role (the runner) has permissions to do so!
     *
     * @param array $newRoles
     * @return void
     */
    public function syncRoles(array $newRoles)
    {
        $roles = Role::whereIn('id', $newRoles)->get();
        $me = Auth::user();
        $myHighestOrder = $me->getHighestRoleOrder();

        //TODO: A permission to edit roles, similar to Discord.
        if (!$this->canBeEdited($me)) {
            throw new Exception("You cannot edit this user");
        }

        //Let's get rid of roles that aren't in the list
        $detach = [];
        foreach ($this->roles as $key => $role) {
            if (!in_array($role->id, $newRoles) && $role->id !== 1) {
                if ($myHighestOrder < $role->order) {
                    $detach[] = $role->id;
                    unset($this->roles[$key]);
                } else {
                    throw new Exception("You don't have the right permissions to remove this role from any user.");
                }
            }
        }
        $this->roles()->detach($detach);

        //Make sure the roles in this list are valid for us to add/remove and then attach them.
        //TODO: Vanity roles are roles any user can apply to themselves, vanity roles DO NOT have permissions.
        foreach ($roles as $role) {
            if (!$this->hasRole($role->id)) {
                if ($me->hasPermission('admin')) { //$role->vanity
                    // Make sure that the role we are adding isn't Members (which every member has duh) and is lower than ours.
                    if ($role->id !== 1 && $myHighestOrder < $role->order) {
                            $this->roles()->attach($role->id); //Alright, great.
                            $this->roles[] = $role;
                    } else {
                        throw new Exception("You don't have the right permissions to add this role to any user. #2");
                    }
                } else {
                    throw new Exception("You don't have the right permissions to add this role to any user.");
                }
            }
        }

        $this->relations['roles'] = $this->roles->sortBy('order');
    }
}
