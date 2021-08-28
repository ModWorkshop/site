<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    private static $membersRole = null;
    
    private $permissions = [];

    // Always return roles for users
    protected $with = ["roles"];
    private $roleNames = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class)->orderByDesc('order');
    }

    public function getRolesAndPermissions() {
        self::$membersRole ??= Role::find(1);
        $roles = $this->roles;
        $roles->prepend(self::$membersRole);

        /**
         * THIS IS TEMPORARY
         * the system should not allow for "permission racing", basically if we deny permission once, it should NOT give it again.
         * Same thing goes with granting, if we give a permission (aside members role), it should not be denied.
         * The purpose of the allow variable is only take away permissions when necessary and not have to do give - take - give.
         * Only give and take with the first give being pretty much the members role.
         */
        foreach ($roles as $role) {
            $this->roleNames[] = $role->name;
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
        }
    }
    
    protected static function booted()
    {
        static::retrieved(function($model) {
            $model->getRolesAndPermissions();
        });
    }

    /**
     * Checks whether the user has a certain permission.
     *
     * @param string $toWhat
     * @return boolean
     */
    function hasPermission(string $toWhat) {
        return isset($this->permissions[$toWhat]) && $this->permissions[$toWhat] === true;
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
        return $this->roleNames;
    }

    public function getPermissionsAttribute() {
        return $this->permissions;
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
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
