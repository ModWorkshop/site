<?php

namespace App\Models;

use App\Notifications\EmailVerificationNotification;
use App\Services\APIService;
use App\Services\Utils;
use App\Traits\Reportable;
use Auth;
use Carbon\Carbon;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Chelout\RelationshipEvents\Traits\HasRelationshipObservables;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
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
 * @property \Illuminate\Support\Carbon|null $last_online
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GameRole[] $allGameRoles
 * @property-read int|null $all_game_roles_count
 * @property-read \App\Models\Ban|null $ban
 * @property-read mixed $last_ban
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Report[] $reports
 * @property-read int|null $reports_count
 * @method static Builder|User whereLastOnline($value)
 * @property string $banner
 * @property string $bio
 * @property bool $private_profile
 * @property string $custom_title
 * @property bool $invisible
 * @property string|null $donation_url
 * @property string $show_tag
 * @property-read \App\Models\Ban|null $gameBan
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ban[] $gameBans
 * @property-read int|null $game_bans_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GameRole[] $gameRoles
 * @property-read int|null $game_roles_count
 * @property-read mixed $last_game_ban
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SocialLogin[] $socialLogins
 * @property-read int|null $social_logins_count
 * @property-read \App\Models\Supporter|null $supporter
 * @method static Builder|User whereBanner($value)
 * @method static Builder|User whereBio($value)
 * @method static Builder|User whereCustomTitle($value)
 * @method static Builder|User whereDonationUrl($value)
 * @method static Builder|User whereInvisible($value)
 * @method static Builder|User wherePrivateProfile($value)
 * @method static Builder|User whereShowTag($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BlockedTag[] $allBlockedTags
 * @property-read int|null $all_blocked_tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BlockedUser[] $allBlockedUsers
 * @property-read int|null $all_blocked_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FollowedGame[] $allFollowedGames
 * @property-read int|null $all_followed_games_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FollowedMod[] $allFollowedMods
 * @property-read int|null $all_followed_mods_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FollowedUser[] $allFollowedUsers
 * @property-read int|null $all_followed_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Thread[] $threads
 * @property-read int|null $threads_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasApiTokens, Notifiable, Reportable;
    use QueryCacheable, HasBelongsToManyEvents, HasRelationshipObservables;

    public $cacheFor = 10;
    public static $membersRole = null;
    public static $flushCacheOnUpdate = true;

    public static $currentGameId = null;
    public static $staticWith;
    protected $saveToReport = ['bio', 'custom_title'];

    public function __construct($attributes = []) {
        if (isset(self::$staticWith)) {
            $this->with = self::$staticWith;
        }
        parent::__construct($attributes);
        if (Auth::hasUser()) {
            $this->with[] = 'blockedByMe';
            $this->with[] = 'blockedMe';
        }
    }

    public static function setCurrentGame(int $gameId)
    {
        Log::info("setting game as " . $gameId);
        self::$currentGameId = $gameId;
        self::$staticWith = ['roles', 'gameRoles', 'ban', 'gameBan'];
    }
    
    // Always return roles for users
    protected $appends = ['color'];
    protected $with = ['roles', 'ban', 'supporter'];

    //Permissions and roles stuff
    private $gameRolesCache = [];
    private $permissions = [];
    private $gamePermissions = [];
    private $permissionsLoaded = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'last_online',
        'custom_color',
        'unique_name',
        'banner',
        'bio',
        'invisible',
        'private_profile',
        'custom_title',
        'donation_url',
        'show_tag',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'roles',
        'password',
        'remember_token',
        'email',
        'email_verified_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_online' => 'datetime',
    ];

    #region Relations

    public function blockedByMe(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, BlockedUser::class, 'block_user_id', 'id', 'id', 'block_user_id')
            ->where('blocked_users.user_id', Auth::user()->id)->limit(1)->without('blockedMe', 'blockedByMe');
    }

    public function blockedMe(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, BlockedUser::class, 'user_id', 'id', 'id', 'user_id')
            ->where('blocked_users.block_user_id', Auth::user()->id)->limit(1)->without('blockedMe', 'blockedByMe');
    }

    /**
     * Returns the follow model (if exists) of the user for the authenticated user
     */
    public function followed() : HasOne
    {
        return $this->hasOne(FollowedUser::class, 'follow_user_id')->where('user_id', Auth::user()->id);
    }

    public function allFollowedGames()
    {
        return $this->hasMany(FollowedGame::class);
    }

    public function followedGames() : BelongsToMany
    {
        return $this->belongsToMany(Game::class, FollowedGame::class);
    }

    public function allFollowedMods()
    {
        return $this->hasMany(FollowedMod::class);
    }
 
    public function followedMods() : BelongsToMany
    {
        return $this->belongsToMany(Mod::class, FollowedMod::class)->select('mods.*');
    }

    public function allFollowedUsers()
    {
        return $this->hasMany(FollowedUser::class);
    }

    public function followedUsers() : BelongsToMany
    {
        return $this->belongsToMany(User::class, FollowedUser::class, null, 'follow_user_id')->select('users.*');
    }

    public function getMorphClass(): string {
        return 'user';
    }

    protected static function booted()
    {
        self::created(fn(User $user) => $user->extra()->create());

        self::deleting(function(User $user) {
            if (isset($user->avatar) && !str_contains($user->avatar, 'http')) {
                Storage::disk('r2')->delete('users/avatars/'.$user->avatar);
            }

            if (isset($user->banner)) {
                Storage::disk('r2')->delete('users/banners/'.$user->banner);
            }

            foreach ($user->mods as $mod) {
                $mod->delete();
            }
        });
    }

    public function allBlockedUsers()
    {
        return $this->hasMany(BlockedUser::class);
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
    public function blockedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, BlockedUser::class, null, 'block_user_id')->withPivot('silent');
    }

    /**
     * The user's blocked tags. Not loaded normally
     */
    public function blockedTags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, BlockedTag::class);        
    }

    public function allBlockedTags()
    {
        return $this->hasMany(BlockedTag::class);
    }

    public function mods(): HasMany
    {
        return $this->hasMany(Mod::class);
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function extra(): HasOne
    {
        return $this->hasOne(UserExtra::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->orderBy('order');
    }
    
    public function allGameRoles(): BelongsToMany
    {
        return $this->belongsToMany(GameRole::class)->orderBy('order');
    }

    public function gameRoles(): BelongsToMany
    {
        return $this->belongsToMany(GameRole::class)->where('game_id', self::$currentGameId)->orderBy('order');
    }

    public function ban() : HasOne
    {
        return $this->hasOne(Ban::class)->where('active', true)->whereNull('game_id');
    }

    public function gameBan(): HasOne
    {
        return $this->hasOne(Ban::class)->where('active', true)->where('game_id', self::$currentGameId);
    }

    public function gameBans(): HasMany
    {
        return $this->hasMany(Ban::class);
    }

    public function supporter(): HasOne
    {
        return $this->hasOne(Supporter::class)->orderBy('expire_date')->without('user');
    }

    public function socialLogins(): HasMany
    {
        return $this->hasMany(SocialLogin::class);
    }

    #endregion

    #region Attributes

    /**
     * Checks if the user has a password and email set to be able to login into the account
     * Used to check whether the user can remove SSO.
     */
    public function signable(): Attribute
    {
        return Attribute::make(fn($value, $attrs) => isset($attrs['password']) && isset($attrs['email']));
    }

    public function activated(): Attribute
    {
        return Attribute::make(fn($value, $attrs) => $attrs['email_verified_at'] || $this->socialLogins()->exists());
    }

    public function customColor(): Attribute
    {
        return Attribute::make(fn($value) =>  preg_replace('/\s+/', '', $value));
    }

    public function activeSupporter(): Attribute
    {
        return Attribute::make(function($value, $attributes) {
            $supporter = $this->supporter;
            if (isset($supporter) && (!isset($supporter->expire_date) || Carbon::now()->lessThan($supporter->expire_date))) {
                return $supporter;
            } else {
                return null;
            }
        });
    }

    public function color(): Attribute
    {
        return Attribute::make(function($value, $attributes) {
            if (isset($attributes['custom_color']) && $this->supporterActive) {
                return $attributes['custom_color'];
            }

            if (isset(self::$currentGameId)) {
                foreach ($this->gameRoles as $role) {
                    if ($role->color) {
                        return $role->color;
                    }
                }
            }
            foreach ($this->roles as $role) {
                if ($role->color) {
                    return $role->color;
                }
            }
        });
    }

    public function tag(): Attribute
    {
        return Attribute::make(function() {
            //Supporter handled in frontend
            if ($this->show_tag === 'none' || (isset($this->activeSupporter) && $this->show_tag === 'supporter_or_role')) {
                return null;
            }

            if (isset(User::$currentGameId)) {
                foreach ($this->gameRoles as $role) {
                    if ($role->tag) {
                        return $role->tag;
                    }
                }    
            }

            foreach ($this->roles as $role) {
                if ($role->tag) {
                    return $role->tag;
                }
            }
        });
    }

    /**
     * User roles but with assured members role.
     */
    public function roleList(): Attribute
    {
        return Attribute::make(function() {
            self::$membersRole ??= Role::with('permissions')->find(1);
            $roles = [...$this->roles];
            if (!in_array(self::$membersRole, $roles)) {
                $roles[] = self::$membersRole;
            }

            return $roles;
        });
    }

    public function permissionList(): Attribute
    {
        return Attribute::make(function() {
            if ($this->permissionsLoaded) {
                return $this->permissions;
            }

            $this->permissions = Utils::collectPermissions($this->roleList);
            
            if ($this->id === 1) {
                return ['admin' => true];
            }

            $this->permissionsLoaded = true;
    
            return $this->permissions;
        });
    }

    /**
     * Returns the "highest" order role
     * Highest goes from lowest to highest. So 0 is the highest.
     * -1, or Members, is returned as null, it could be seen as "infinite" as it will awlays be the lowest.
     * @return int|null
     */
    public function highestRoleOrder(): Attribute
    {
        return Attribute::make(function() {
            //!Special case for the Super Admin!//
            if ($this->id === 1) {
                return pow(10, 10);
            } else {
                $highest = null;
                foreach ($this->roles as $role) {
                    $roleOrder = $role->order;
                    if ($role->id != 1 && $roleOrder !== null && ($highest === null || $roleOrder > $highest)) {
                        $highest = $roleOrder;
                    }
                }

                return $highest;
            }
    
        });
    }
    
    public function getLastGameBanAttribute()
    {
        if (isset(self::$currentGameId)) {
            $ban = $this->gameBan;
            if (isset($ban) && isset($ban->case)) {
                if (!isset($ban->expire_date) || Carbon::now()->lessThan($ban->expire_date)) {
                    return $ban;
                }
            }
        }

        return null;
    }

    public function getLastBanAttribute()
    {
        if ($this->relationLoaded('ban') && !$this->hasPermission('moderate-users')) {
            $ban = $this->ban;
            if (isset($ban)) {
                if ($ban->active && (!isset($ban->expire_date) || Carbon::now()->lessThan($ban->expire_date))) {
                    return $ban;
                }
            }
        }

        return null;
    }


    #endregion

    #region Methods

    /**
     * Returns specific game's roles
     *
     * @return Collection<Game>
     */
    public function getGameRoles(int $gameId, bool $withPerms=false)
    {
        if (isset($this->gameRolesCache[$gameId])) {
            return $this->gameRolesCache[$gameId];
        }

        if ($withPerms && !$this->relationLoaded('allGameRoles.permission')) {
            $this->load('allGameRoles.permission');
        }

        $gameRoles = $this->allGameRoles;

        $gameRoles = $gameRoles->where('game_id', $gameId);
        $this->gameRolesCache[$gameId] = $gameRoles;

        return $gameRoles;
    }

    public function getGamePerms(int $gameId): array
    {
        if (isset($this->gamePermissions[$gameId])) {
            return $this->gamePermissions[$gameId];
        }

        $this->gamePermissions[$gameId] = Utils::collectPermissions($this->getGameRoles($gameId, true));

        return $this->gamePermissions[$gameId];
    }

    public function hasGameRole(int $gameId, int $id): bool
    {
        $gameRoles = $this->getGameRoles($gameId);

        foreach ($gameRoles as $role) {
            if ($role->id === $id) {
                return true;
            }
        }

        return false;
    }

    public function hasRole(int $id): bool
    {
        //Everyone has the member role, always.
        if ($id === 1) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->id === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether the user has a certain permission.
     * First it checks if the user has admin permission, admin bypasses all permissions at the moment.
     * Guests are permission-less meaning that if we want to let them see something, that thing must have no needed permissions.
     * A banned user always returns false and makes the user act like a guest, sort of. The frontend should make it clearer.
     */
    function hasPermission(string $toWhat, Game $game=null, bool $byPassBanCheck=false): bool {
        $this->roles->loadMissing('permissions');
        $perms = $this->permissionList;

        //Admin bypasses all
        if ($this->existsAndTrue($perms, 'admin')) {
            return true;
        }

        //Users who moderate users cannot be banned.
        if (!$byPassBanCheck && !$this->existsAndTrue($perms, 'moderate-users') && isset($this->last_ban)) {
            return false;
        }

        //Check game first
        if (isset($game)) {
            if (!isset(self::$currentGameId)) {
                APIService::setCurrentGame($game);
            }
            $gamePerms = $this->getGamePerms($game->id);
            //Game version of the admin permission
            Log::info($this->existsAndTrue($gamePerms, 'moderate-users'));

            if ($this->existsAndTrue($gamePerms, 'manage-game')) {
                return true;
            }
            if (!$byPassBanCheck && !$this->existsAndTrue($gamePerms, 'moderate-users') && isset($this->last_game_ban)) {
                return false;
            }
            if ($this->existsAndTrue($gamePerms, $toWhat)) {
                return true;
            }
        }

        return $this->existsAndTrue($perms, $toWhat);
    }

    private function existsAndTrue(array $perms, string $toWhat)
    {
        return isset($perms[$toWhat]) && $perms[$toWhat] === true;
    }

    public function getGameHighestOrder(int $gameId)
    {
        //!Special case for the Super Admin!//
        if ($this->id === 1) {
            return pow(10, 10);
        } else {
            $highest = null;
            $gameRoles = $this->getGameRoles($gameId);
            foreach ($gameRoles as $role) {
                if ($highest === null || $role->order > $highest) {
                    $highest = $role->order;
                }
            }

            return $highest;
        }

    }

    /**
     * Returns whether or not the user can be edited by the "other" user.
     * The other user defaults to the currently authenticated user
     */
    public function canBeEdited()
    {
        $me = Auth::user();

        //Not signed in? BTFU
        if (!isset($me)) {
            return false;
        }

        //If we try to edit ourselves then return true
        if ($me->id === $this->id) {
            return true;
        }

        $myHighestOrder = $me->highestRoleOrder;
        $highestOrder = $this->highestRoleOrder;

        //We can't edit other users without permission and order
        //A role without an order is invalid, the only one that's allowed to not have one is Member.
        if (!$me->hasPermission('manage-users') || !isset($myHighestOrder)) {
            return false;
        }

        //We have permission to manage user and now let's make sure our order is higher.
        return !$highestOrder || $myHighestOrder > $highestOrder;
    }

    /**
     * A safe way of setting and saving roles
     * The function makes sure that whoever gives the role (the runner) has permissions to do so!
     * !The only roles users are able to give to themselves are vanity roles!
     *
     * !See syncGameRoles if you update this function!
     */
    public function syncRoles(array $newRoles)
    {
        $detach = [];
        $attach = [];

        $me = Auth::user();
        $canManageRoles = $me->hasPermission('manage-roles');
        $myHighestOrder = $me->highestRoleOrder;

        if ($me->id !== $this->id && !$canManageRoles) {
            throw new Exception("You cannot edit roles of other users.");
        }

        //Make sure the roles we try to fetch match the roles we are trying to set!
        $roles = Role::whereIn('id', $newRoles)->get();
        if (count($roles) !== count($newRoles)) {
            throw new Exception("One or more of the roles given are invalid! " . count($roles) . ' != ' . count($newRoles) );
        }

        //Handles removal of roles that aren't present in $newRoles. Makes sure we can remove them.
        foreach ($this->roles as $role) {
            if (!in_array($role->id, $newRoles) && $role->id !== 1) {
                if ($role->is_vanity || ($canManageRoles && $myHighestOrder > $role->order)) {
                    $detach[] = $role->id;
                } else {
                    throw new Exception("You don't have the right permissions to remove this role from any user.");
                }
            }
        }

        //Handles addition of roles that are present in $newRoles. Makes sure we can add them.
        foreach ($roles as $role) {
            if (!$this->hasRole($role->id)) {
                // Make sure that the role we are adding isn't Members (which every member has duh) and is lower than ours.
                if ($role->id !== 1 && (($canManageRoles && $myHighestOrder > $role->order) || $role->is_vanity)) {
                    $attach[] = $role->id;
                } else {
                    throw new Exception("You don't have the right permissions to add this role to any user.");
                }
            }
        }

        //Finally, let's detach and attach the roles we collected. All should be checked properly.
        $rolesRelation = $this->roles();
        $rolesRelation->detach($detach);
        $rolesRelation->attach($attach);

        Role::flushQueryCache();
        $this->load('roles');
    }

    /**
     * Sets the game roles of a user while making sure the auth user can do so.
     * Makes sure not to remove other game's roles. 
     * $newRoles must be valid with all roles being from said game.
     * 
     * It is very similar to the regular syncRoles function, but to keep proper separation, I decided to not use the same code.
     * It makes sure things don't just mixed up.
     * 
     * !See syncRoles if you update this function!
     */
    public function syncGameRoles(Game $game, array $newRoles)
    {
        $detach = [];
        $attach = [];

        $me = Auth::user();
        $canManageRoles = $me->hasPermission('manage-roles', $game);
        $myHighestOrder = $me->getGameHighestOrder($game->id);

        if ($me->id !== $this->id && !$canManageRoles) {
            throw new Exception("You cannot edit roles of other users.");
        }

        //Make sure the roles we try to fetch are the game's roles and nothing else. The count must match!
        $roles = GameRole::where('game_id', $game->id)->whereIn('id', $newRoles)->get();
        if (count($roles) !== count($newRoles)) {
            throw new Exception("One or more of the roles given are invalid!");
        }

        //Handles removal of roles that aren't present in $newRoles. Makes sure we can remove them.
        foreach ($this->getGameRoles($game->id) as $role) {
            if (!in_array($role->id, $newRoles)) {
                if ($role->is_vanity || ($canManageRoles && $myHighestOrder > $role->order)) {
                    $detach[] = $role->id;
                } else {
                    throw new Exception("You don't have the right permissions to remove this role from any user.");
                }
            }
        }

        //Handles addition of roles that are present in $newRoles. Makes sure we can add them.
        foreach ($roles as $role) {
            if (!$this->hasGameRole($game->id, $role->id)) {
                if ($role->is_vanity || ($canManageRoles && $myHighestOrder > $role->order)) {
                    $attach[] = $role->id;
                } else {
                    throw new Exception("You don't have the right permissions to add this role to any user.");
                }
            }
        }

        //Finally, let's detach and attach the roles we collected. All should be checked properly.
        $gameRolesRelation = $this->allGameRoles();
        $gameRolesRelation->detach($detach);
        $gameRolesRelation->attach($attach);

        GameRole::flushQueryCache();
    }

    #endregion
}
