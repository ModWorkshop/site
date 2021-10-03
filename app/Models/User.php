<?php

namespace App\Models;

use Auth;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public static $membersRole = null;
    
    // Always return roles for users
    protected $with = ['roles'];
    private $permissions  = [];
    private $roleNames = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar'
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

    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(Role::class)->orderByDesc('order');
    }

    /**
     * A scope to select permissions for users
     * In most cases, we don't really have a need to know a user's permissions.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithPermissions(Builder $query) : Builder
    {
        return $query->without('roles')->with(['roles.permissions']);
    }

    public function getRoleNames()
    {
        if ($this->gotRoles) {
            return $this->roleNames;
        }

        self::$membersRole ??= Role::with('permissions')->find(1);
        $roles = $this->roles;
        if (!$roles->contains(self::$membersRole)) {
            $roles->prepend(self::$membersRole);
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
            $roles->prepend(self::$membersRole);
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

    public function hasRole(string $roleName)
    {
        return in_array($roleName, $this->rolenames);
    }

    /**
     * Checks whether the user has a certain permission.
     *
     * @param string $toWhat
     * @return boolean
     */
    function hasPermission(string $toWhat) {
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

    protected $appends = ['role_names', 'permissions'];

    public function getRoleNamesAttribute() {
        return $this->getRoleNames();
    }

    public function getPermissionsAttribute() {
        return $this->getPermissions();
    }

    /**
     * Returns the highest order role
     * Similiarly to Discord, we can do things to user that are *below* this order
     * Let's say we have an admin, then a moderator: The admin can set the roles of the mod but mod can't set the admin.
     * @return void
     */
    public function highestRoleOrder()
    {
        /**
         * @var Role|null
         */
        $highest = null;
        foreach ($this->role as $role) {
            if (!$highest || $role->order > $highest->order) {
                $highest = $role;
            }
        }
        return $role?->order ?? -1;
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
        $roles = Role::whereIn('id', $newRoles);
        $me = Auth::user();
        $myHighestOrder = $me->getHighestOrder();
        $highestOrder = $this->getHighestOrder();

        //TODO: A permission to edit roles, similar to Discord.
        //TODO: should we make user #1 the "super admin"?
        if ($me->id !== $this->uid && (!$me->hasPermission('admin') || $myHighestOrder <= $highestOrder)) {
            throw new Exception("You cannot edit this user");
        }

        //Make sure the roles in this list are valid for us to add/remove and then attach them.
        //TODO: Vanity roles are roles any user can apply to themselves, vanity roles DO NOT have permissions.
        foreach ($roles as $role) {
            if ($me->hasPermission('admin')) { //$role->vanity
                // Make sure that the role we are adding isn't Members (which every member has duh) and is lower than ours.
                if ($role->id !== 1 && $role->order < $myHighestOrder) {
                    if (!$this->hasRole($role)) {
                        $this->roles()->attach($role); //Alright, great.
                    }
                } else {
                    throw new Exception("You don't have the right permissions to add this role to any user.");
                }
            } else {
                throw new Exception("You don't have the right permissions to add this role to any user.");
            }
        }

        //Let's get rid of roles that aren't in the list
        $detach = [];
        foreach ($this->roles as $role) {
            if (!in_array($role->id, $newRoles)) {
                $detach[] = $role;
            }
        }
        $this->roles()->detatch($detach);
    }
}
