<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Storage;

/**
 * App\Models\Image
 *
 * @property int $id
 * @property int $user_id
 * @property bool $has_thumb
 * @property string $file
 * @property string $type
 * @property int $size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereHasThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUserId($value)
 * @mixin \Eloquent
 * @property int $mod_id
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereModId($value)
 * @property-read \App\Models\Mod $mod
 */
class Image extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getMorphClass(): string {
        return 'image';
    }

    public function mod() : BelongsTo
    {
        return $this->belongsTo(Mod::class);
    }

    protected static function booted() {
        static::deleting(function (Image $image)
        {
            $mod = $image->mod;
            $updatedMod = false;
            if ($mod->banner && $mod->banner->id === $image->id) {
                $mod->banner_id = null;
                $updatedMod = true;
            }

            if ($mod->thumbnail && $mod->thumbnail->id === $image->id) {
                $mod->thumbnail_id = null;
                $updatedMod = true;
            }

            if ($updatedMod) {
                $mod->save();
            }

            Storage::delete('mods/images/'.$image->file);
            Storage::delete('mods/images/thumbnail_'.$image->file);
        });
    }
}
