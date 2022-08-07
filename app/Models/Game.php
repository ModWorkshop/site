<?php

namespace App\Models;

use App\Services\ModService;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Rennokki\QueryCache\Traits\QueryCacheable;

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
 */
class Game extends Model
{
    use HasFactory, Filterable, QueryCacheable;

    public $cacheFor = 10;

    protected $guarded = [];

    protected $hidden = [];
    
    protected $with = [];

    public function getBreadcrumbAttribute()
    {
        $gameUrl = $this->short_name ?? $this->id;

        return [
            [
                'name' => $this->name,
                'href' => "/game/{$gameUrl}"
            ]
        ];
    }
}