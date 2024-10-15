<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Image newModelQuery()
 * @method static Builder|Image newQuery()
 * @method static Builder|Image query()
 * @method static Builder|Image whereCreatedAt($value)
 * @method static Builder|Image whereFile($value)
 * @method static Builder|Image whereHasThumb($value)
 * @method static Builder|Image whereId($value)
 * @method static Builder|Image whereSize($value)
 * @method static Builder|Image whereType($value)
 * @method static Builder|Image whereUpdatedAt($value)
 * @method static Builder|Image whereUserId($value)
 * @property int $mod_id
 * @method static Builder|Image whereModId($value)
 * @property-read Mod $mod
 * @property int $display_order
 * @property bool $visible
 * @method static Builder|Image whereDisplayOrder($value)
 * @method static Builder|Image whereVisible($value)
 * @mixin Eloquent
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
