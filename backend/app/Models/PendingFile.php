<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $type
 * @property int $size
 * @property bool $completed
 * @property string|null $fileable_type
 * @property int|null $fileable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereFileableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereFileableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereUpdatedAt($value)
 * @property int $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereUserId($value)
 * @property string $name
 * @property string $file_name
 * @property int $mod_id
 * @property int|null $file_id
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingFile whereName($value)
 * @property-read \App\Models\File|null $file
 * @property-read \App\Models\Mod $mod
 * @property-read \App\Models\User $user
 * @property string $file_type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingFile whereFileType($value)
 * @mixin \Eloquent
 */
class PendingFile extends Model
{
    protected $guarded = [];

    public function getMorphClass(): string {
        return 'pending_file';
    }

    public function mod() : BelongsTo
    {
        return $this->belongsTo(Mod::class);
    }

    public function file() : BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
