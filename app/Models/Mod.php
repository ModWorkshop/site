<?php

namespace App\Models;

use App\Services\ModService;
use App\Traits\Filterable;
use App\Traits\RelationsListener;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Rennokki\QueryCache\Traits\QueryCacheable;

abstract class Visibility {
    const pub = 1;
    const hidden = 2;
    const unlisted = 3;
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
 * @property string $access_ids
 * @property bool $suspended
 * @property bool $comments_disabled
 * @property int $file_status
 * @property float $score
 * @property string|null $bumped_at
 * @property string|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Category|null $game
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static \Database\Factories\ModFactory factory(...$parameters)
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
 * @mixin \Eloquent
 * @property-read void $breadcrumb
 * @property-read mixed $tag_ids
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $images
 * @property-read int|null $images_count
 * @property int|null $download_id
 * @property string|null $download_type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $files
 * @property-read int|null $files_count
 * @method static Builder|Mod whereDownloadId($value)
 * @method static Builder|Mod whereDownloadType($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read Model|\Eloquent $download
 * @method static Builder|Mod whereUserId($value)
 * @property-read \App\Models\Image|null $thumbnail
 * @property int|null $banner_id
 * @property-read \App\Models\Image|null $banner
 * @property-read bool $liked
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Link[] $links
 * @property-read int|null $links_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModMember[] $members
 * @property-read int|null $members_count
 * @method static Builder|Mod whereBannerId($value)
 * @method static Builder|Mod whereLikes($value)
 * @method static Builder|Mod whereBumpedAt($value)
 * @method static Builder|Mod wherePublishedAt($value)
 * @property-read \App\Models\TransferRequest|null $transferRequest
 * @property int|null $last_user_id
 * @property-read \App\Models\User|null $lastUser
 * @method static Builder|Mod whereLastUserId($value)
 */
class Mod extends Model
{
    use HasFactory, RelationsListener, Filterable, QueryCacheable;

    public $cacheFor = 1;
    public static $flushCacheOnUpdate = true;

    /**
     * The attributes that aren't mass assignable.
     * download_type/id are handled by Laravel
     *
     * @var array
     */
    protected $guarded = ['download_type', 'download_id'];

    private $withFull = ['user.extra', 'tags', 'images', 'files', 'links', 'members', 'banner', 'lastUser'];
    protected $with = ['user', 'game', 'category', 'thumbnail', 'members'];
    protected $appends = [];
    protected $hidden = ['download_type'];

    protected $casts = [
        'bumped_at' => 'datetime',
        'published_at' => 'datetime',
    ];
    
    public function withAllRest()
    {
        $this->append('breadcrumb');
        $this->loadMissing($this->withFull);
        $this->category?->loadMissing('parent');
        $this->members?->loadMissing('extra');
    }

    protected static function booted() {
        static::deleting(function (Mod $mod)
        {
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
        });
        static::creating(function (Mod $mod)
        {
            $mod->bumped_at = $mod->freshTimestampString();
        });
    }

    public function scopeList(Builder $query)
    {
        return $query->without(['tags']);
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
        return $this->hasOne(Image::class);
    }
        
    public function banner()
    {
        return $this->hasOne(Image::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function files()
    {
        return $this->hasMany(File::class)->orderByDesc('updated_at');
    }

    public function links()
    {
        return $this->hasMany(Link::class)->orderByDesc('updated_at');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'mod_members')->withPivot(['level', 'accepted', 'created_at']);
    }

    public function getMemberLevel(int $userId, $acceptedOnly=true)
    {
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
     * but this should be used only when not loading the files relationship for optimization
     *
     * @return MorphTo
     */
    public function download() : MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Returns an array with breadcrumbs
     *
     * @return array
     */
    public function getBreadcrumbAttribute()
    {
        return [
            [
                'name' => $this->name,
                'is_mod' => true,
                'href' => "/mod/{$this->id}"
            ],
            ...ModService::makeBreadcrumb($this->game, $this->category)
        ];
    }

    public function liked()
    {
        return $this->hasOne(ModLike::class)->where('user_id', Auth::id());
    }

    /**
     * Checks the files/links of the mod and sets the file status accordingly
     *
     * @param boolean $save
     * @return void
     */
    public function calculateFileStatus(bool $save=true)
    {
        $foundOneActive = false;
        $foundOneWaiting = false;

        //Links have no approval state for now.
        //For music mods we generally disable this feature due to problems regarding the uncertainty of the file
        if (count($this->links) > 0) {
            $foundOneActive = true;
        }

        foreach ($this->files as $file) {
            if ($file->approved) {
                $foundOneActive = true;
                break;
            } else {
                $foundOneWaiting = true;
            }
        }

        if ($foundOneActive) {
            $this->file_status = 1;
        } elseif ($foundOneWaiting) {
            $this->file_status = 2;
        } else {
            $this->file_status = 0;
        }

        //If we don't have a publish date and status is 1
        if (!$this->published_at && $this->file_status == 1) {
            $this->published_at = $this->freshTimestampString();
            $this->bumped_at = $this->published_at;
        }

        if ($save) {
            $this->save();
        }
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
