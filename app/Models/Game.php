<?php

namespace App\Models;

use App\Services\APIService;
use Arr;
use Auth;
use Carbon\Carbon;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Chelout\RelationshipEvents\Traits\HasRelationshipObservables;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Resources\MissingValue;
use Log;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\QueryBuilder\QueryBuilder;

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
 * @method static \Illuminate\Database\Eloquent\Builder|Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game query()
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereButtons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereDisporder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereLastDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereWebhookUrl($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Forum|null $forum
 * @property int|null $forum_id
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereForumId($value)
 * @property-read \App\Models\FollowedGame|null $followed
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mod[] $mods
 * @property-read int|null $mods_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GameRole[] $roles
 * @property-read int|null $roles_count
 * @property-read mixed $user_data
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereModsCount($value)
 */
class Game extends Model
{
    use HasFactory;
    use QueryCacheable, HasBelongsToManyEvents, HasRelationshipObservables;

    public $cacheFor = 60;
    public static $flushCacheOnUpdate = true;

    protected $guarded = [];

    protected $hidden = ['webhook_url'];
    
    protected $appends = ['user_data', 'announcements'];
    
    protected $with = ['followed', 'roles'];

    protected $casts = [
        'last_date' => 'datetime',
    ];

    public function getMorphClass(): string {
        return 'game';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (!isset($value)) {
            return null;
        }

        $game = QueryBuilder::for(Game::class)
            ->allowedIncludes(['roles', 'forum'])
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
        return $this->hasOne(Forum::class);
    }

    public function mods(): HasMany
    {
        return $this->hasMany(Mod::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(GameRole::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Returns whether the game is followed by the authenticated user
     */
    public function followed() : HasOne
    {
        return $this->hasOne(FollowedGame::class)->where('user_id', Auth::user()?->id);
    }

    public function userData(): Attribute
    {
        return Attribute::make(function() {
            $user = Auth::user();
            return isset($user) ? [
                'role_ids' => array_values(array_unique(Arr::pluck($user->getGameRoles($this->id), 'id'))),
                'highest_role_order' => $user->getGameHighestOrder($this->id),
                'permissions' => $user->getGamePerms($this->id),
                'ban' => $user->last_game_ban
            ] : new MissingValue;
        });
    }

    public function waitingCount(): Attribute
    {
        return Attribute::make(fn() => $this->mods()->where('approved', false)->count());

    }

    public function reportsCount(): Attribute
    {
        return Attribute::make(fn() => $this->reports()->whereArchived(false)->count());
    }
        

    public function announcements(): Attribute
    {
        return Attribute::make(fn() => APIService::getAnnouncements($this));
    }

    public function ensureForumExists()
    {
        if (!isset($this->forum_id) && $this->id) {
            $forum = $this->forum()->create([
                'game_id' => $this->id
            ]);
            $this->forum_id = $forum->id;
            $this->save();
        }
    }

    public static function booted()
    {
        static::creating(function(Game $game) {
            if (!isset($game->last_date)) {
                $game->last_date = Carbon::now();
            }
        });

        static::saving(fn(Game $game) => $game->ensureForumExists());
        static::created(fn(Game $game) => $game->ensureForumExists());
    }
}