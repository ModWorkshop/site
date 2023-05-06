<?php

namespace App\Models;

use Arr;
use Auth;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Models\ForumCategory
 *
 * @property int $id
 * @property string $name
 * @property string $desc
 * @property int $forum_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereForumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereUpdatedAt($value)
 * @property string $emoji
 * @property-read \App\Models\Forum $forum
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereEmoji($value)
 * @property bool $is_private
 * @property bool $private_threads
 * @property bool $banned_can_post
 * @property string|null $announce_until
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GameRole[] $gameRoles
 * @property-read int|null $game_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereAnnounceUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereBannedCanPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory wherePrivateThreads($value)
 * @mixin \Eloquent
 */
class ForumCategory extends Model
{
    use HasFactory;

    public function getMorphClass(): string {
        return 'forum_category';
    }

    protected $guarded = [];
    protected $with = ['roles', 'gameRoles'];
    protected $appends = ['role_policies', 'game_role_policies', 'can_post'];

    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    public function rolePolicies(): Attribute
    {
        return Attribute::make(function() {
            $roles = [];
            foreach ($this->roles as $role) {
                $roles[$role->id] = ['can_view' => $role->pivot->can_view, 'can_post' => $role->pivot->can_post];
            }

            return (object)$roles;
        });
    }

    public function gameRolePolicies(): Attribute
    {
        return Attribute::make(function() {
            $roles = [];
            foreach ($this->gameRoles as $role) {
                $roles[$role->id] = ['can_view' => $role->pivot->can_view, 'can_post' => $role->pivot->can_post];
            }

            return (object)$roles;
        });
    }
 
    public function roles(): MorphToMany
    {
        return $this->morphedByMany(Role::class, 'role', 'forum_category_roles')->withPivot(['can_view', 'can_post']);
    }

    public function gameRoles(): MorphToMany
    {
        return $this->morphedByMany(GameRole::class, 'role', 'forum_category_roles')->withPivot(['can_view', 'can_post']);
    }

    /**
     * Returns whether or not the user can view the forum category.
     * If private, the user must have at least wone role that allows them to view it. Guests are denied.
     */
    public function canView(): Attribute
    {
        return Attribute::make(function() {
            $user = Auth::user();
    
            if (isset($user) && $user->hasPermission('manage-discussions')) {
                return true;
            }
    
            $canView = true;
    
            foreach ($this->roles as $role) {
                if ($role->id === 1 || (isset($user) && $user->hasRole($role->id))) {
                    if ($role->id !== 1 && $role->pivot->can_view === true) {
                        return true;
                    } else if ($role->pivot->can_view === false) {
                        $canView = false;
                    }
                }
            }
    
            if (isset($this->game_id)) {
                foreach ($this->gameRoles as $role) {
                    if ((isset($user) && $user->hasGameRole($this->game_id, $role->id))) {
                        if ($role->id !== 1 && $role->pivot->can_view === true) {
                            return true;
                        } else if ($role->pivot->can_view === false) {
                            $canView = false;
                        }
                    }
                }
            }
    
            return $canView && !$this->is_private;
        });
    }

    /**
     * Returns whether or not the user can post in the forum category.
     * If the forum category is private, the user must have at least one role that allows them to post.
     */
    public function canPost(): Attribute
    {
        return Attribute::make(function() {
            $user = Auth::user();
            
            //Only users can post of course
            if (!isset($user)) {
                return false;
            }
    
            if ($user->hasPermission('manage-discussions')) {
                return true;
            }
    
            $canPost = true;
    
            foreach ($this->roles as $role) {
                if ($role->id === 1 || (isset($user) && $user->hasRole($role->id))) {
                    if ($role->id !== 1 && $role->pivot->can_post === true) {
                        return true;
                    } else if ($role->pivot->can_post === false) {
                        $canPost = false;
                    }
                }
            }
    
            if (isset($this->game_id)) {
                foreach ($this->gameRoles as $role) {
                    if ((isset($user) && $user->hasGameRole($this->game_id, $role->id))) {
                        if ($role->id !== 1 && $role->pivot->can_post === true) {
                            return true;
                        } else if ($role->pivot->can_post === false) {
                            $canPost = false;
                        }
                    }
                }
            }
    
            return $canPost && !$this->is_private;
        });
    }
}
