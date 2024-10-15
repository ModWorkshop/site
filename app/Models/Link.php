<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Link
 *
 * @property int $id
 * @property int $user_id
 * @property int $mod_id
 * @property string $name
 * @property string $desc
 * @property string $label
 * @property string $url
 * @property string $version
 * @property int|null $image_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Mod $mod
 * @property-read User $user
 * @method static Builder|Link newModelQuery()
 * @method static Builder|Link newQuery()
 * @method static Builder|Link query()
 * @method static Builder|Link whereCreatedAt($value)
 * @method static Builder|Link whereDesc($value)
 * @method static Builder|Link whereId($value)
 * @method static Builder|Link whereImageId($value)
 * @method static Builder|Link whereLabel($value)
 * @method static Builder|Link whereModId($value)
 * @method static Builder|Link whereName($value)
 * @method static Builder|Link whereUpdatedAt($value)
 * @method static Builder|Link whereUrl($value)
 * @method static Builder|Link whereUserId($value)
 * @method static Builder|Link whereVersion($value)
 * @mixin Eloquent
 */
class Link extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['mod'];
    protected $with = ['user'];

    public function getMorphClass(): string {
        return 'link';
    }

    public function mod() : BelongsTo
    {
        return $this->belongsTo(Mod::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function downloadsRelation() : MorphMany
    {
        return $this->morphMany(DownloadableDownload::class, 'downloadable');
    }

    protected static function booted() {
        static::deleted(function (Link $link)
        {
            $mod = $link->mod;

            if ($mod->download_type === Link::class && $mod->download_id === $link->id) {
                $mod->download_id = null;
                $mod->download_type = null;
            }

            $mod->calculateFileStatus();
        });
    }
}
