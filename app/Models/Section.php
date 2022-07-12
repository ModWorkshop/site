<?php

namespace App\Models;

use App\Services\ModService;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Section extends Model
{
    use HasFactory, Filterable;
    protected $guarded = [];

    protected $hidden = ['parent', 'game'];
    
    protected $with = [];

    protected $appends = ['path'];

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

    public function getBreadcrumbAttribute($includeGame=null)
    {
        return ModService::makeBreadcrumb(ModService::categoryCrumb($this), $this->game_id, $this->parent_id, $includeGame ?? true);
    }
}