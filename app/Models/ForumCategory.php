<?php

namespace App\Models;

use Arr;
use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\ForumCategory
 *
 * @property int $id
 * @property string $name
 * @property string $desc
 * @property int $forum_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ForumCategory newModelQuery()
 * @method static Builder|ForumCategory newQuery()
 * @method static Builder|ForumCategory query()
 * @method static Builder|ForumCategory whereCreatedAt($value)
 * @method static Builder|ForumCategory whereDesc($value)
 * @method static Builder|ForumCategory whereForumId($value)
 * @method static Builder|ForumCategory whereId($value)
 * @method static Builder|ForumCategory whereName($value)
 * @method static Builder|ForumCategory whereUpdatedAt($value)
 * @property string $emoji
 * @property-read Forum $forum
 * @method static Builder|ForumCategory whereEmoji($value)
 * @property bool $is_private
 * @property bool $private_threads
 * @property bool $banned_can_post
 * @property string|null $announce_until
 * @property-read Collection|GameRole[] $gameRoles
 * @property-read int|null $game_roles_count
 * @property-read Collection|Role[] $roles
 * @property-read int|null $roles_count
 * @method static Builder|ForumCategory whereAnnounceUntil($value)
 * @method static Builder|ForumCategory whereBannedCanPost($value)
 * @method static Builder|ForumCategory whereIsPrivate($value)
 * @method static Builder|ForumCategory wherePrivateThreads($value)
 * @property int $display_order
 * @property-read mixed $can_post
 * @property-read mixed $can_view
 * @property-read mixed $game_role_policies
 * @property-read mixed $role_policies
 * @method static Builder|ForumCategory whereDisplayOrder($value)
 * @property bool $hidden
 * @property bool $can_close_threads
 * @property bool $grid_mode
 * @method static Builder|ForumCategory whereCanCloseThreads($value)
 * @method static Builder|ForumCategory whereGridMode($value)
 * @method static Builder|ForumCategory whereHidden($value)
 * @mixin Eloquent
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
