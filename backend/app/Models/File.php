<?php

namespace App\Models;

use App\Jobs\ScanArchivePaths;
use App\Services\Utils;
use Arr;
use Eloquent;
use Http;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Kiwilan\Archive\Archive;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Storage;
use z4kn4fein\SemVer\Version;

/**
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
 * @property string|null $legacy_mime
 * @method static Builder<static>|File whereLegacyMime($value)
 * @mixin Eloquent
 */

const ALLOWED_ARCHIVE_TYPES = [
    'zip' => true,
    'rar' => true,
    '7z' => true
];

class File extends Model
{
    use HasFactory;

    protected $guarded = [];
    // I'd like to make archive_paths public later, but it can be quite long so I may need to think of a better way
    // Graphql would've been sweet as REST APIs suck in this kind of thing
    protected $hidden = ['mod', 'semver_version', 'legacy_mime', 'archive_paths'];
    protected $with = [];

    protected $casts = [
        'semver_version' => 'string',
        'archive_paths' => 'array'
    ];

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
            return Storage::disk('s3')->url('mods/files/'.$this->file)."?filename={$encode}";
        });
    }

    public function detectFileModType(): string {
        $gameDef = $this->mod->game->mod_types_definition;

        if (is_null($gameDef)) {
            return 'unknown';
        }

        if (is_null($this->archive_paths)) {
            return 'unknown_not_archive';
        }

        $gameDef = json_decode($gameDef, true);
        $rootPath = null;
        foreach ($this->archive_paths as $path => $item) {
            $splt = explode( '/', ltrim($path, '/'));

            $isDirectory = Arr::get($item, 'isDirectory', false);
            if (count($splt) === 1 && !$isDirectory) {
                return 'unknown'; // The root contains files.
            }

            if ($rootPath == null) {
                $rootPath = $splt[0];
            } else if ($splt[0] !== $rootPath) { // The root contains more than a single folder
                return 'unknown';
            }
        }

        foreach ($gameDef as $kind => $def) {
            foreach ($this->archive_paths as $path => $item) {
                $containsBlacklisted = false;
                $containsAny = false;

                $withoutRoot = str_replace($rootPath.'/', '', $path);

                if (isset($def['doesnt_contain'])) {
                    foreach ($def['doesnt_contain'] as $folder) {
                        if (str_starts_with($withoutRoot, $folder)) {
                            $containsBlacklisted = true;
                            break;
                        }
                    }
                }

                if ($containsBlacklisted) {
                    break; // Can't be this kind...
                }

                foreach ($def['contains_any'] as $folder) {
                    if (str_starts_with($withoutRoot, $folder)) {
                        $containsAny = true;
                        break;
                    }
                }

                if ($containsAny) {
                    return $kind;
                }
            }
        }

        return 'unknown';
    }

    protected static function booted() {
        static::saving(function(File $file) {
            if (!empty($file->version) && Version::parseOrNull($file->version) != null) {
                $file->semver_version = $file->version;
            } else {
                $file->semver_version = null;
            }

            if ($file->isDirty('archive_paths')) {
                $file->mod_type = $file->detectFileModType();
            }
        });

        static::saved(function(File $file) {
            if ($file->id && $file->isDirty('file')) {
                ScanArchivePaths::dispatch($file);
            }
        });

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
