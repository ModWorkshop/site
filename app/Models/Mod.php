<?php

namespace App\Models;

use App\Services\ModService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Mod
 *
 * @property int $id
 * @property int|null $thumbnail_id
 * @property int|null $category_id
 * @property int|null $game_id
 * @property int $submitter_uid
 * @property string $name
 * @property string $desc
 * @property string $short_desc
 * @property string $changelog
 * @property string $license
 * @property string $instructions
 * @property mixed|null $depends_on
 * @property int $visibility
 * @property string $banner
 * @property string $url
 * @property int $downloads
 * @property int $views
 * @property string $version
 * @property string $donation
 * @property string $access_ids
 * @property bool $suspended
 * @property bool $comments_disabled
 * @property int $file_status
 * @property float $score
 * @property string|null $bump_date
 * @property string|null $publish_date
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
 * @method static Builder|Mod whereBanner($value)
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
 */
class Mod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $with = ['tags', 'submitter', 'game', 'category'];
    protected $appends = ['tag_ids', 'breadcrumb'];
    
    public function scopeList(Builder $query)
    {
        return $query->without(['tags']);
    }

    public function submitter() : HasOne 
    {
        return $this->hasOne(User::class, "id", 'submitter_uid');
    }

    public function category() : HasOne 
    {
        return $this->hasOne(Category::class, "id", 'category_id');
    }

    public function game() : HasOne
    {
        return $this->hasOne(Category::class, "id", 'game_id');
    }

    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Returns an array with breadcrumbs
     *
     * @return void
     */
    public function getBreadcrumbAttribute($includeGame=null)
    {
        return ModService::makeBreadcrumb([
            'name' => $this->name,
            'is_mod' => true,
            'href' => "/mod/{$this->id}"
        ], $this->game_id, $this->category_id, $includeGame ?? true);
    }

    public function getTagIdsAttribute()
    {
        if ($this->relationLoaded('tags')) {
            $tagIds = [];
            foreach ($this->tags as $tag) {
                $tagIds[] = $tag->id;
            }
            return $tagIds;
        } else {
            return null;
        }
    }
}
