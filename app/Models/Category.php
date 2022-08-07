<?php

namespace App\Models;

use App\Services\ModService;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Rennokki\QueryCache\Traits\QueryCacheable;

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
 * @method static \Database\Factories\CategoryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereApprovalOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereButtons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDisporder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereGrid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereLastDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereWebhookUrl($value)
 * @mixin \Eloquent
 * @property-read mixed $breadcrumb
 */
class Category extends Model
{
    use HasFactory, Filterable, QueryCacheable;

    public $cacheFor = 10;
    public static $flushCacheOnUpdate = true;

    protected $guarded = [];

    protected $hidden = ['parent', 'game'];
    
    protected $with = [];

    protected $appends = [];

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
}
