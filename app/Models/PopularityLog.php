<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PopularityLog
 *
 * @property int $id
 * @property string $type
 * @property int $mod_id
 * @property int $user_id
 * @property string $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereUserId($value)
 * @property-read \App\Models\Mod $mod
 * @property-read \App\Models\User $user
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PopularityLog extends Model
{
    use HasFactory;
    const CREATED_AT = null;

    protected $guarded = [];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mod(): BelongsTo
    {
        return $this->belongsTo(Mod::class);
    }
    
    static function log(Mod $mod, string $type) {
        $user = Auth::user();

        $ipAddress = request()->ip();
        $modId = $mod->id;
        $userId = $user?->id;

        $log = PopularityLog::where('mod_id', $modId)
            ->where('type', $type)
            ->where(fn($q) => $q->where('user_id', $userId)->orWhere('ip_address', $ipAddress))
            ->first();

        if (isset($log)) {
            $log->update([
                'updated_at' => Carbon::now(),
                'user_id' => $userId,
                'ip_address' => $ipAddress
            ]);
        } else {
            PopularityLog::create([
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'mod_id' => $modId,
                'type' => $type,
            ]);
        }
    }

    static function deleteLog(Mod $mod, string $type)
    {
        $user = Auth::user();

        PopularityLog::where('mod_id', $mod->id)
            ->where('type', $type)
            ->where(fn($q) => $q->when(isset($user))->where('user_id', $user->id)->orWhere('ip_address', request()->ip()))
            ->delete();
    }
}
