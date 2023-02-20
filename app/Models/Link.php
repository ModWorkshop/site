<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Mod $mod
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Link newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Link newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Link query()
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereVersion($value)
 * @mixin \Eloquent
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
