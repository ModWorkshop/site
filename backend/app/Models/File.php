<?php

namespace App\Models;

use App\Services\Utils;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Storage;
use Str;


/**
 *
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
 * @property string $label
 * @property string $version
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $display_order
 * @property int $downloads
 * @property string|null $semver_version
 * @property-read mixed $download_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DownloadableDownload> $downloadsRelation
 * @property-read int|null $downloads_relation_count
 * @property-read mixed $file_ext
 * @property-read \App\Models\Image|null $image
 * @property-read \App\Models\Mod $mod
 * @property-read mixed $safe_file_name
 * @property-read \App\Models\User $user
 * @method static Builder<static>|File newModelQuery()
 * @method static Builder<static>|File newQuery()
 * @method static Builder<static>|File query()
 * @method static Builder<static>|File whereCreatedAt($value)
 * @method static Builder<static>|File whereDesc($value)
 * @method static Builder<static>|File whereDisplayOrder($value)
 * @method static Builder<static>|File whereDownloads($value)
 * @method static Builder<static>|File whereFile($value)
 * @method static Builder<static>|File whereId($value)
 * @method static Builder<static>|File whereImageId($value)
 * @method static Builder<static>|File whereLabel($value)
 * @method static Builder<static>|File whereModId($value)
 * @method static Builder<static>|File whereName($value)
 * @method static Builder<static>|File whereSemverVersion($value)
 * @method static Builder<static>|File whereSize($value)
 * @method static Builder<static>|File whereType($value)
 * @method static Builder<static>|File whereUpdatedAt($value)
 * @method static Builder<static>|File whereUserId($value)
 * @method static Builder<static>|File whereVersion($value)
 * @mixin Eloquent
 */
class File extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = ['mod'];
    protected $with = [];

    protected $appends = ['download_url', 'type'];

    public function getMorphClass(): string {
        return 'file';
    }

    public function mod() : BelongsTo
    {
        return $this->belongsTo(Mod::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function image() : BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function downloadsRelation() : MorphMany
    {
        return $this->morphMany(DownloadableDownload::class, 'downloadable');
    }

    public function type(): Attribute {
        return new Attribute(fn() => implode('.', array_slice(explode('.', $this->file_ext), -1, 1)));
    }

    public function fileExt(): Attribute
    {
        return new Attribute(fn() => Utils::safeFileType($this->file));
    }

    public function safeFileName(): Attribute
    {
        return Attribute::make(function() {
            $name = Utils::safeFileName($this->name);
            $ext = $this->file_ext;
            if (!empty($ext)) {
                $name = "{$name}.{$this->file_ext}";
            }
            return $name;
        });
    }

    public function downloadUrl(): Attribute
    {
        return Attribute::make(function() {
            $encode = rawurlencode($this->safeFileName);
            return Storage::disk('s3')->url('mods/files/'.$this->file)."?response-content-disposition=attachment&filename={$encode}";
        });
    }


    protected static function booted() {
        static::deleting(function(File $file) {
            Storage::delete('mods/files/'.$file->file);
        });

        static::deleted(function(File $file) {
            $mod = $file->mod;

            if ($mod->download_type === File::class && $mod->download_id === $file->id) {
                $mod->download_id = null;
                $mod->download_type = null;
            }

            $mod->calculateFileStatus(); // Saved here
        });
    }
}
