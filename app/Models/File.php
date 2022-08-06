<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Storage;

/**
 * App\Models\File
 *
 * @property int $id
 * @property int $user_id
 * @property int $mod_id
 * @property string $name
 * @property string $desc
 * @property string $file
 * @property string $type
 * @property int|null $image_id
 * @property int $size
 * @property bool $approved
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File query()
 * @method static \Illuminate\Database\Eloquent\Builder|File whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Mod $mod
 * @property string $label
 * @property string $version
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|File whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereVersion($value)
 */
class File extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = ['mod'];
    protected $with = ['user'];

    public function mod() : BelongsTo
    {
        return $this->belongsTo(Mod::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted() {
        static::deleting(function (File $file)
        {
            Storage::delete('mods/files/'.$file->file);

            $mod = $file->mod;
            
            if ($mod->download_type === File::class && $mod->download_id === $file->id) {
                $mod->download_id = null;
                $mod->download_type = null;
                $mod->calculateFileStatus();
            }
        });
    }
}
