<?php

namespace App\Models;

use App\Services\ModService;
use App\Traits\Filterable;
use App\Traits\RelationsListener;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
 * @property-read \App\Models\User|null $submitter
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
 * @method static Builder|Mod whereSubmitterUid($value)
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
 * @method static Builder|Mod whereSubmitterId($value)
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
 */
class Mod extends Model
{
    use HasFactory, RelationsListener, Filterable;

    /**
     * The attributes that aren't mass assignable.
     * download_type/id are handled by Laravel
     *
     * @var array
     */
    protected $guarded = ['download_type', 'download_id'];

    protected $with = ['tags', 'submitter', 'game', 'category', 'images', 'files', 'links', 'members', 'thumbnail', 'banner'];
    protected $appends = ['breadcrumb', 'liked'];
    protected $hidden = ['download_type'];

    protected $casts = [
        'bumped_at' => 'datetime',
        'published_at' => 'datetime',
    ];
    
    protected static function booted() {
        static::creating(function (Mod $mod)
        {
            $mod->bumped_at = $mod->freshTimestampString();
        });
    }

    public function scopeList(Builder $query)
    {
        return $query->without(['tags']);
    }

    public function submitter() : HasOne 
    {
        return $this->hasOne(User::class, "id", 'user_id');
    }

    public function category() : HasOne 
    {
        return $this->hasOne(Category::class, "id", 'category_id');
    }

    public function game() : HasOne
    {
        return $this->hasOne(Category::class, "id", 'game_id');
    }
    
    public function thumbnail() : HasOne
    {
        return $this->hasOne(Image::class, 'id', 'thumbnail_id');
    }
        
    public function banner() : HasOne
    {
        return $this->hasOne(Image::class, 'id', 'banner_id');
    }

    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function images() : HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function files() : HasMany
    {
        return $this->hasMany(File::class)->orderByDesc('updated_at');
    }

    public function links() : HasMany
    {
        return $this->hasMany(Link::class)->orderByDesc('updated_at');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'mod_members')->withPivot(['level', 'accepted', 'created_at']);
    }

    public function getActiveMember(User $user)
    {
        $members = $this->members();
        
        foreach ($members as $member) {
            if ($member->pivot->active && $member->user->id === $user->id) {
                return $member;
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
    public function getBreadcrumbAttribute($includeGame=null)
    {
        return ModService::makeBreadcrumb([
            'name' => $this->name,
            'is_mod' => true,
            'href' => "/mod/{$this->id}"
        ], $this->game_id, $this->category_id, $includeGame ?? true);
    }


    /**
     * Returns whether or not the user has liked the mod, if the user is not logged in automatically returns false
     *
     * @return boolean
     */
    public function getLikedAttribute()
    {
        if (!Auth::check()) {
            return false;
        }

        return ModLike::where('user_id', Auth::id())->where('mod_id', $this->id)->exists();
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
}
