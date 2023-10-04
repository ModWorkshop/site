<?php

namespace App\Models;

use App\Services\ModService;
use Carbon\Carbon;
use Database\Factories\CategoryFactory;
use Eloquent;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Storage;
/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property string|null $short_name
 * @property string $desc
 * @property bool $hidden
 * @property bool $grid
 * @property int $disporder
 * @property int|null $parent_id
 * @property int|null $game_id
 * @property string $thumbnail
 * @property string $banner
 * @property string $buttons
 * @property string $webhook_url
 * @property bool $approval_only
 * @property string $last_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Category|null $game
 * @property-read mixed $path
 * @property-read Category|null $parent
 * @method static CategoryFactory factory(...$parameters)
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereApprovalOnly($value)
 * @method static Builder|Category whereBanner($value)
 * @method static Builder|Category whereButtons($value)
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereDesc($value)
 * @method static Builder|Category whereDisporder($value)
 * @method static Builder|Category whereGameId($value)
 * @method static Builder|Category whereGrid($value)
 * @method static Builder|Category whereHidden($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereLastDate($value)
 * @method static Builder|Category whereName($value)
 * @method static Builder|Category whereParentId($value)
 * @method static Builder|Category whereShortName($value)
 * @method static Builder|Category whereThumbnail($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @method static Builder|Category whereWebhookUrl($value)
 * @property-read mixed $breadcrumb
 * @mixin Eloquent
 */
class Category extends Model
{
    use Cachable;

    protected $guarded = [];

    protected $hidden = ['parent', 'game'];

    protected $with = [];

    protected $appends = [];

    protected $casts = [
        'last_date' => 'datetime',
    ];

    public function getMorphClass(): string {
        return 'category';
    }

    public function getPathAttribute()
    {
        // Paths are shown after selecting a game, therefore we don't really need to include the game in them
        $breadcrumb = $this->getBreadcrumbAttribute(includeGame: false);
        $path = '';

        foreach ($breadcrumb as $crumb) {
            if (empty($path)) {
                $path = $crumb['name'];
            } else {
                $path = $crumb['name'] . ' / ' . $path;
            }
        }

        return $path;
    }

    public function game() : HasOne
    {
        return $this->hasOne(Game::class, "id", 'game_id');
    }

    public function parent() : HasOne
    {
        return $this->hasOne(Category::class, "id", 'parent_id');
    }

    public function getBreadcrumbAttribute($includeGame=true)
    {
        return ModService::makeBreadcrumb($includeGame ? $this->game : null, $this);
    }

    public function toSearchableArray()
    {
        $this->with(['parent', 'game']);

        return [
            'name' => $this->name
        ];
     }

    public static function booted()
    {
        static::creating(function(Category $cat) {
            if (!isset($cat->last_date)) {
                $cat->last_date = Carbon::now();
            }
        });
    }
}
