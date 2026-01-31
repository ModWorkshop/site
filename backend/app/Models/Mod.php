<?php

namespace App\Models;

use App\Interfaces\SubscribableInterface;
use App\Services\APIService;
use App\Services\ModService;
use App\Services\Utils;
use App\Traits\RelationsListener;
use App\Traits\Reportable;
use App\Traits\Subscribable;
use Auth;
use Carbon\Carbon;
use Database\Factories\ModFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Log;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Spatie\QueryBuilder\QueryBuilder;

abstract class Visibility {
    public const public = 'public';
    public const private = 'private';
    public const unlisted = 'unlisted';
}

/**
 * App\Models\Mod
 *
 * @property int $id
 * @property int|null $thumbnail_id
 * @property int|null $category_id
 * @property int|null $game_id
 * @property int $user_id
 * @property string $name
 * @property string $desc
 * @property string $short_desc
 * @property string $changelog
 * @property string $license
 * @property string $instructions
 * @property mixed|null $depends_on
 * @property int $visibility
 * @property string $legacy_banner_url
 * @property string $url
 * @property int $downloads
 * @property int $views
 * @property int $likes
 * @property string $version
 * @property string $donation
 * @property bool $suspended
 * @property bool $comments_disabled
 * @property int $file_status
 * @property float $score
 * @property string|null $bumped_at
 * @property string|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Category|null $category
 * @property-read Game|null $game
 * @property-read User|null $user
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @method static ModFactory factory(...$parameters)
 * @method static Builder|Mod list()
 * @method static Builder|Mod newModelQuery()
 * @method static Builder|Mod newQuery()
 * @method static Builder|Mod query()
 * @method static Builder|Mod whereAccessIds($value)
 * @method static Builder|Mod whereLegacyBannerUrl($value)
 * @method static Builder|Mod whereBumpDate($value)
 * @method static Builder|Mod whereCategoryId($value)
 * @method static Builder|Mod whereChangelog($value)
 * @method static Builder|Mod whereCommentsDisabled($value)
 * @method static Builder|Mod whereCreatedAt($value)
 * @method static Builder|Mod whereDependsOn($value)
 * @method static Builder|Mod whereDesc($value)
 * @method static Builder|Mod whereDonation($value)
 * @method static Builder|Mod whereDownloads($value)
 * @method static Builder|Mod whereFileStatus($value)
 * @method static Builder|Mod whereGameId($value)
 * @method static Builder|Mod whereId($value)
 * @method static Builder|Mod whereInstructions($value)
 * @method static Builder|Mod whereLicense($value)
 * @method static Builder|Mod whereName($value)
 * @method static Builder|Mod wherePublishDate($value)
 * @method static Builder|Mod whereScore($value)
 * @method static Builder|Mod whereShortDesc($value)
 * @method static Builder|Mod whereUserUid($value)
 * @method static Builder|Mod whereSuspended($value)
 * @method static Builder|Mod whereThumbnailId($value)
 * @method static Builder|Mod whereUpdatedAt($value)
 * @method static Builder|Mod whereUrl($value)
 * @method static Builder|Mod whereVersion($value)
 * @method static Builder|Mod whereViews($value)
 * @method static Builder|Mod whereVisibility($value)
 * @property-read void $breadcrumb
 * @property-read mixed $tag_ids
 * @property-read Collection|Image[] $images
 * @property-read int|null $images_count
 * @property int|null $
 * _id
 * @property string|null $download_type
 * @property-read Collection|File[] $files
 * @property-read int|null $files_count
 * @method static Builder|Mod whereDownloadId($value)
 * @method static Builder|Mod whereDownloadType($value)
 * @property-read Collection|Comment[] $comments
 * @property-read int|null $comment_count
 * @property-read Model|Eloquent $download
 * @method static Builder|Mod whereUserId($value)
 * @property-read Image|null $thumbnail
 * @property int|null $banner_id
 * @property-read Image|null $banner
 * @property-read bool $liked
 * @property-read Collection|Link[] $links
 * @property-read int|null $links_count
 * @property-read Collection|ModMember[] $members
 * @property-read int|null $members_count
 * @method static Builder|Mod whereBannerId($value)
 * @method static Builder|Mod whereLikes($value)
 * @method static Builder|Mod whereBumpedAt($value)
 * @method static Builder|Mod wherePublishedAt($value)
 * @property-read TransferRequest|null $transferRequest
 * @property int|null $last_user_id
 * @property-read User|null $lastUser
 * @method static Builder|Mod whereLastUserId($value)
 * @property string $access_ids
 * @property-read Follows|null $follows
 * @property-read Follows|null $followsUsingCategory
 * @property-read Suspension|null $lastSuspension
 * @property-read FollowedMod|null $followed
 * @property-read Collection|Taggable[] $tagsSpecial
 * @property-read int|null $tags_special_count
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection|FollowedMod[] $followers
 * @property-read int|null $followers_count
 * @property-read Subscription|null $subscribed
 * @property bool $has_download
 * @property bool $approved
 * @method static Builder|Mod whereApproved($value)
 * @method static Builder|Mod whereHasDownload($value)
 * @property int|null $instructs_template_id
 * @property-read Collection|Dependency[] $dependencies
 * @property-read int|null $dependencies_count
 * @property-read InstructsTemplate|null $instructsTemplate
 * @property-read Collection|User[] $membersThatCanEdit
 * @property-read int|null $members_that_can_edit_count
 * @property-read Collection|Report[] $reports
 * @property-read int|null $reports_count
 * @method static Builder|Mod whereInstructsTemplateId($value)
 * @property float $daily_score
 * @property float $weekly_score
 * @property int|null $allowed_storage
 * @method static Builder|Mod whereAllowedStorage($value)
 * @method static Builder|Mod whereDailyScore($value)
 * @method static Builder|Mod whereWeeklyScore($value)
 * @property int|null $download_id
 * @property-read BlockedUser|null $blockedByMe
 * @property-read int|null $comments_count
 * @property bool $disable_mod_managers
 * @property int|null $background_id
 * @property float $background_opacity
 * @property-read \App\Models\Image|null $background
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $downloadRelation
 * @property-read mixed $mod_managers
 * @property-read \App\Models\ModMember|null $selfMember
 * @method static Builder|Mod whereBackgroundId($value)
 * @method static Builder|Mod whereBackgroundOpacity($value)
 * @method static Builder|Mod whereDisableModManagers($value)
 * @property int $parser_version
 * @method static Builder|Mod whereParserVersion($value)
 * @property-read mixed $current_storage
 * @property-read mixed $used_storage
 * @property string|null $repo_url
 * @property-read \App\Models\IgnoredGame|null $gameIgnoredByMe
 * @method static Builder<static>|Mod whereRepoUrl($value)
 * @mixin Eloquent
 */
class Mod extends Model implements SubscribableInterface
{
    use HasFactory, RelationsListener, Subscribable, Reportable;

    public static $allowedIncludes = [
        'category',
        'thumbnail',
        'background',
        'user',
        'tags',
        'images',
        'files',
        'links',
        'game',
        'download',
        'members',
        'banner',
        'lastUser',
        'liked',
        'transferRequest',
        'subscribed',
        'dependencies',
        'instructsTemplate',
    ];

    public static $allowedFields = [
        'id',
        'category_id',
        'game_id',
        'user_id',
        'name',
        'desc',
        'short_desc',
        'changelog',
        'license',
        'instructions',
        'visibility',
        'legacy_banner_url',
        'downloads',
        'likes',
        'views',
        'version',
        'donation',
        'suspended',
        'comments_disabled',
        'score',
        'daily_score',
        'weekly_score',
        'bumped_at',
        'published_at',
        'download_id',
        'download_type',
        'last_user_id',
        'has_download',
        'approved',
        'thumbnail_id',
        'background_id',
        'background_opacity',
        'banner_id',
        'allowed_storage',
        'updated_at',
        'created_at',
    ];

    public $commentsOrder = 'DESC';

    /**
     * The attributes that aren't mass assignable.
     * download_type/id are handled by Laravel
     *
     * @var array
     */
    protected $guarded = ['download_type', 'download_id'];
    protected $saveToReport = ['desc', 'version', 'short_desc'];

    public const DEFAULT_MOD_WITH = [
        'user',
        'game'
    ];

    public const LIST_MOD_WITH = [
        'category',
        'thumbnail',
        'tags',
    ];

    public $fullLoad = false;

    // Gets loaded in mod page
    public const SHOW_MOD_WITH = [
        'game',
        'user',
        'tags',
        'images',
        'banner',
        'lastUser',
        'liked',
        'transferRequest',
        'subscribed',
        'dependencies',
        'instructsTemplate',
        'members',
        'category',
        'thumbnail',
        'background'
    ];

    protected $with = self::DEFAULT_MOD_WITH;
    protected $appends = [];
    protected $hidden = [];

    protected $casts = [
        'bumped_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        $mod = QueryBuilder::for(Mod::class)->allowedFields(Mod::$allowedFields)->allowedIncludes(Mod::$allowedIncludes);
        return $mod->findOrFail($value);
    }

    public function getMorphClass(): string {
        return 'mod';
    }

    public function withFetchResourceGame()
    {
        $this->append('breadcrumb');
        $this->fullLoad = true; // Files and links handled in resource
        $this->loadMissing(self::SHOW_MOD_WITH);
        $this->append('download');
        if (Auth::hasUser()) {
            $this->loadMissing('followed');
            $this->loadMissing('ignored');
        }
        if ($this->suspended) {
            $this->loadMissing('lastSuspension');
        }
    }

    protected static function booted() {
        static::retrieved(function(Mod $mod) {
            app('siteState')->addMod($mod);
        });

        static::deleting(function(Mod $mod) {
            if ($mod->approved == null) {
                // Send to discord about this
                $send = [Setting::getValue('discord_approval_webhook')];
                if (count($send)) {
                    Utils::sendDiscordMessage($send, "The mod **%s** which was waiting for approval, was deleted.", [$mod->name]);
                }
            }

            foreach ($mod->comments as $comment) {
                $comment->delete();
            }
            //While there's onDelete cascade, this unfortunately doesn't work with event hooking.
            //Basically we must do this or images won't be deleted!
            foreach ($mod->images as $image) {
                $image->delete();
            }
            foreach ($mod->files as $file) {
                $file->delete();
            }
            $mod->subscriptions()->delete();
        });
        static::creating(function(Mod $mod) {
            $mod->bumped_at ??= $mod->freshTimestampString();
        });

        static::created(function(Mod $mod) {
            //Auto-sub ourselves
            if ($mod->user->extra->auto_subscribe_to_mod) {
                $mod->subscriptions()->create([
                    'user_id' => $mod->user_id
                ]);
            }
        });
    }

    public function scopeList(Builder $query)
    {
        return $query->without(['tags']);
    }

    public function followers()
    {
        return $this->hasMany(FollowedMod::class)->with('user');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lastUser()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function thumbnail()
    {
        return $this->belongsTo(Image::class);
    }

    public function background()
    {
        return $this->belongsTo(Image::class);
    }

    public function banner()
    {
        return $this->belongsTo(Image::class);
    }

    public function tagsSpecial(): HasMany
    {
        return $this->hasMany(Taggable::class, 'taggable_id')->where('taggable_type', 'mod');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')->orderByRaw('taggables.created_at');
    }

    public function images()
    {
        return $this->hasMany(Image::class)->orderByRaw('display_order ASC, created_at ASC');
    }

    public function files() : HasMany
    {
        return $this->hasMany(File::class);
    }

    public function links()
    {
        return $this->hasMany(Link::class)->orderByRaw("display_order DESC, updated_at DESC");
    }

    public function selfMember()
    {
        $user = Auth::user();
        return $this->hasOne(ModMember::class)
            ->when(isset($user), fn($q) => $q->where('user_id', $user->id))
            ->where('accepted', true);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'mod_members')->withPivot(['level', 'accepted', 'created_at']);
    }

    /**
     * Depenencies of the mod for the instructions
     */
    public function dependencies() : MorphMany
    {
        return $this->morphMany(Dependency::class, 'dependable');
    }

    /**
     * Instructions template in the mod
     */
    public function instructsTemplate() : BelongsTo
    {
        return $this->belongsTo(InstructsTemplate::class);
    }

    public function membersThatCanEdit()
    {
        return $this->belongsToMany(User::class, 'mod_members')
            ->withPivot(['level', 'accepted', 'created_at'])
            ->wherePivot('level', '<=', 1)
            ->wherePivot('accepted', true);
    }

    public function getMemberLevel(int $userId, $acceptedOnly=true)
    {
        if ($userId === $this->user_id) {
            return 'owner';
        }

        $members = $this->members;

        foreach ($members as $member) {
            if ((!$acceptedOnly || $member->pivot->accepted) && $member->id === $userId) {
                return $member->pivot->level;
            }
        }

        return null;
    }

    public function comments() : MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('reply_to');
    }

    public function transferRequest()
    {
        return $this->hasOne(TransferRequest::class);
    }

    /**
     * Returns the primary download same as the attribute,
     *
     * @return MorphTo
     */
    public function downloadRelation() : MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'download_type', 'download_id');
    }

    /**
     * Returns an array with breadcrumbs
     *
     * @return array
     */
    public function getBreadcrumbAttribute()
    {
        if (!$this->relationLoaded('game') || !$this->relationLoaded('category')) {
            return [];
        }

        return [
            [
                'name' => $this->game->name,
                'id' => $this->game->short_name ?? $this->game->id,
                'type' => 'game'
            ],
            ...$this->category?->computed_breadcrumb ?? [],
            [
                'name' => $this->name,
                'id' => $this->id,
                'type' => 'mod'
            ]
        ];
    }

    public function filesCount(): Attribute {
        return Attribute::make(fn() => $this->withSecureConstraints(fn() => $this->files()->count()))->shouldCache();
    }

    public function linksCount(): Attribute {
        return Attribute::make(fn() => $this->withSecureConstraints(fn() => $this->links()->count()))->shouldCache();
    }

    public function usedStorage(): Attribute {
        return Attribute::make(fn() => intval($this->withSecureConstraints(fn() => $this->files()->sum('size'))));
    }

    public function maxStorage(): Attribute {
        return Attribute::make(function() {
            $size = $this->allowed_storage ?? Setting::getValue('mod_storage_size');

            if (isset($this->user->hasSupporterPerks)) {
                $size = max($size, Setting::getValue('supporter_mod_storage_size'));
            }

            return $size;
        });
    }

    public function currentStorage(): Attribute {
        return Attribute::make(fn() => $this->maxStorage - $this->usedStorage);
    }

    public function modManagers(): Attribute {
        return Attribute::make(function() {
            $gameId = $this->game_id;
            return ModManager::where(function($q) {
                    $user = Auth::user();
                    if (!$user?->extra->developer_mode) {
                        $q->where('hidden', false);
                    }
                })->where(fn($q) => $q->where('game_id', $gameId)->orWhereHasIn('games', fn($q) => $q->where('game_id', $gameId)))
            ->get();
        });
    }

    /**
     * Smartly returns current download ($this->download)
     * In case it's not loaded, tries to calculate it using download_id and download_type
     * If download_id is not set, it will return either first link or first file.
     */
    public function download(): Attribute {
        return Attribute::make(function() {
            $linksLoaded = $this->relationLoaded('links');
            $filesLoaded = $this->relationLoaded('files');

            // If download exists, just return it
            if ($this->relationLoaded('downloadRelation') || !($linksLoaded && $filesLoaded)) {
                if (isset($this->downloadRelation)) {
                    return $this->downloadRelation;
                }
            }

            $id = $this->download_id;
            $type = $this->download_type;
            $filesCount = $this->files_count;
            $linksCount = $this->links_count;
            $hasPrimary = isset($id) && isset($type);


            // Has no files or links
            if ($filesCount == 0 && $linksCount == 0) {
                return null;
            }

            // Has primary download and both links and files relations are loaded
            if ($hasPrimary) {
                if ($type == 'link') {
                    if ($linksLoaded && ($link = $this->links->find($id))) {
                        return $link;
                    } else {
                        return $this->withSecureConstraints(fn() => $this->links()->find($id));
                    }
                } else {
                    if ($filesLoaded && ($link = $this->files->find($id))) {
                        return $link;
                    } else {
                        return $this->withSecureConstraints(fn() => $this->files()->find($id));
                    }
                }
            }

            //Has only a single file/link so just return it
            if (abs($filesCount - $linksCount) === 1) {
                if ($linksLoaded && $link = $this->links[0]) {
                    return $link;
                } else if ($filesLoaded) {
                    return $this->files[0];
                }else {
                    return $this->withSecureConstraints(fn() => $this->links()->first() ?? $this->files()->first());
                }
            }
        });
    }

    public function liked()
    {
        return $this->hasOne(ModLike::class)->where('user_id', Auth::id());
    }

    public function blockedByMe()
    {
        return $this->hasOne(BlockedUser::class, 'block_user_id', 'user_id')->where('user_id', Auth::id());
    }

    public function gameIgnoredByMe()
    {
        return $this->hasOne(IgnoredGame::class, 'game_id', 'game_id')->where('user_id', Auth::id());
    }

    /**
     * Returns whether the mod is ignored by the authenticated user
     */
    public function ignored() : HasOne
    {
        return $this->hasOne(IgnoredMod::class)->where('user_id', Auth::user()?->id)->without('mod');
    }

    /**
     * Returns the follow model (if exists) of the mod for the authenticated user
     */
    public function followed() : HasOne
    {
        return $this->hasOne(FollowedMod::class)->where('user_id', Auth::user()?->id);
    }

    public function lastSuspension() : HasOne
    {
        return $this->hasOne(Suspension::class)->where('status', true);
    }

    /**
     * Checks the files/links of the mod and sets the file status accordingly
     *
     * @param boolean $save
     * @return void
     */
    public function calculateFileStatus(bool $save=true)
    {
        $this->has_download = $this->withSecureConstraints(fn() => $this->files()->count()) > 0 || $this->withSecureConstraints(fn() => $this->links()->count()) > 0;

        if ($save) {
            $this->save();
        }
    }

    public function publish(bool $save=true)
    {
        if (isset($this->published_at) || !$this->approved || !$this->has_download || $this->visibility != Visibility::public) {
            return;
        }

        $this->published_at = Carbon::now();
        $this->bumped_at = $this->freshTimestampString();

        if ($save) {
            $this->save();
        }

        $game = $this->game;
        $category = $this->category;

        //Send to main webhook and webhooks that were set by game or category
        $send = [Setting::getValue('discord_webhook'), $game->webhook_url, $category?->webhook_url];

        if (count($send)) {
            $siteUrl = env('FRONTEND_URL');
            Utils::sendDiscordMessage($send, "The mod **%s** is now public for the first time in **%s**. {$siteUrl}/mod/%s", [
                $this->name,
                ($game ? $game->name : 'NA').($category ? '/'.$category->name : ''),
                $this->id
            ]);
        }

        // Update the game's last date since the mod was published
        $game->update([
            'last_date' => Carbon::now()
        ]);
    }

    public function bump($save=true)
    {
        $this->bumped_at = Carbon::now();

        $userId = Auth::user()->id;

        //If we are the owner or one of the members, show ourselves as the last updater. Otherwise, show no one.
        if ($this->user->id === $userId || $this->getMemberLevel($userId) !== null) {
            $this->last_user_id = $userId;
        } else {
            $this->last_user_id = null;
        }

        if ($save) {
            $this->save();
        }
    }
}
