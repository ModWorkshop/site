<?php

namespace App\Models;

use App\Services\APIService;
use App\Services\ModService;
use Arr;
use Auth;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Resources\MissingValue;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Spatie\QueryBuilder\QueryBuilder;
use Storage;

/**
 * App\Models\Game
 *
 * @property int $id
 * @property string $name
 * @property string|null $short_name
 * @property int $disporder
 * @property string $thumbnail
 * @property string $banner
 * @property string $buttons
 * @property string $webhook_url
 * @property string $last_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $breadcrumb
 * @method static Builder|Game newModelQuery()
 * @method static Builder|Game newQuery()
 * @method static Builder|Game query()
 * @method static Builder|Game whereBanner($value)
 * @method static Builder|Game whereButtons($value)
 * @method static Builder|Game whereCreatedAt($value)
 * @method static Builder|Game whereDisporder($value)
 * @method static Builder|Game whereId($value)
 * @method static Builder|Game whereLastDate($value)
 * @method static Builder|Game whereName($value)
 * @method static Builder|Game whereShortName($value)
 * @method static Builder|Game whereThumbnail($value)
 * @method static Builder|Game whereUpdatedAt($value)
 * @method static Builder|Game whereWebhookUrl($value)
 * @property-read Forum|null $forum
 * @property int|null $forum_id
 * @method static Builder|Game whereForumId($value)
 * @property-read FollowedGame|null $followed
 * @property-read Collection|Mod[] $mods
 * @property-read int|null $mod_count
 * @property-read Collection|GameRole[] $roles
 * @property-read int|null $roles_count
 * @property-read mixed $user_data
 * @method static Builder|Game whereModsCount($value)
 * @property-read int|null $mods_count
 * @property-read Collection<int, Report> $reports
 * @property-read int|null $reports_count
 * @method static Builder|Game whereModCount($value)
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @mixin Eloquent
 */
class Game extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = ['webhook_url', 'viewable_mods_count'];
    protected $appends = [];
    protected $with = ['followed'];

    protected $casts = [
        'last_date' => 'datetime',
    ];

    public function relationsToArray()
    {
        $attributes = [];

        foreach ($this->getArrayableRelations() as $key => $value) {
            // If the values implement the Arrayable interface we can just call this
            // toArray method on the instances which will convert both models and
            // collections to their proper array form and we'll set the values.
            if ($value instanceof Arrayable) {
                \Log::channel('single')->info('Key: ' . $key);
            
                $relation = $value->toArray();
            }

            // If the value is null, we'll still go ahead and set it in this list of
            // attributes, since null is used to represent empty relationships if
            // it has a has one or belongs to type relationships on the models.
            elseif (is_null($value)) {
                $relation = $value;
            }

            // If the relationships snake-casing is enabled, we will snake case this
            // key so that the relation attribute is snake cased in this returned
            // array to the developers, making this consistent with attributes.
            if (static::$snakeAttributes) {
                $key = \Str::snake($key);
            }

            // If the relation value has been set, we will set it on this attributes
            // list for returning. If it was not arrayable or null, we'll not set
            // the value on the array because it is some type of invalid value.
            if (isset($relation) || is_null($value)) {
                $attributes[$key] = $relation;
            }

            unset($relation);
        }

        return $attributes;
    }

    public function toArray()
    {
        return array_merge($this->attributesToArray(), $this->relationsToArray());
    }

    public function getMorphClass(): string {
        return 'game';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (!isset($value)) {
            return null;
        }

        $game = QueryBuilder::for(Game::class)
            ->allowedIncludes(['roles', 'forum', 'categories'])
            ->with('forum');

        if (is_numeric($value)) {
            $game = $game->where('id', $value);
        } else {
            $game = $game->where('short_name', $value);
        }

        return $game->firstOrFail();
    }

    public function forum() : HasOne
    {
        return $this->hasOne(Forum::class)->without('game');
    }

    // Mods that the current user is able to see
    public function viewableMods(): HasMany
    {
        $mods = $this->hasMany(Mod::class)->without('game');;
        ModService::viewFilters($mods);
        return $mods;
    }

    public function mods(): HasMany
    {
        return $this->hasMany(Mod::class)->without('game');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class)->without('game');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->without('game');
    }

    public function roles(): HasMany
    {
        return $this->hasMany(GameRole::class)->without('game');
    }

    public function reports()
    {
        return $this->hasMany(Report::class)->without('game');
    }

    public function ownModManagers()
    {
        return $this->hasMany(ModManager::class)->without('game');
    }

    function modManagers() {
        return $this->belongsToMany(ModManager::class)->without('game');
    }

    /**
     * Returns whether the game is followed by the authenticated user
     */
    public function followed() : HasOne
    {
        return $this->hasOne(FollowedGame::class)->where('user_id', Auth::user()?->id)->without('game');
    }

    public function userData(): Attribute
    {
        return Attribute::make(function() {
            $user = Auth::user();
            return isset($user) ? [
                'role_ids' => array_values(array_unique(Arr::pluck($user->getGameRoles($this->id), 'id'))),
                'highest_role_order' => $user->getGameHighestOrder($this->id),
                'permissions' => $user->getGamePerms($this->id),
                'ban' => $user->getLastGameban($this->id)
            ] : new MissingValue;
        });
    }

    public function waitingCount(): Attribute
    {
        return Attribute::make(fn() => $this->mods()->whereApproved(null)->count());

    }

    public function reportCount(): Attribute
    {
        return Attribute::make(fn() => $this->reports()->whereArchived(false)->count());
    }


    public function announcements(): Attribute
    {
        return Attribute::make(fn() => APIService::getAnnouncements($this));
    }

    public function ensureForumExists()
    {
        // Checking just in case because this does fail sometimes (like in migrations)
        if (!isset($this->forum_id) && $this->id && Game::where('id', $this->id)->exists()) {
            $forum = $this->forum()->create([
                'game_id' => $this->id
            ]);
            $this->forum_id = $forum->id;
            $this->save();
        }
    }

    public static function booted()
    {
        static::saving(fn(Game $game) => $game->ensureForumExists());
        static::created(fn(Game $game) => $game->ensureForumExists());

        static::deleting(function(Game $game) {
            Storage::delete('games/images/'.$game->banner);
            Storage::delete('games/images/'.$game->thumbnail);
            $game->categories()->delete();
            $game->tags()->delete();
        });
    }
}
