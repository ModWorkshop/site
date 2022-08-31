<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TransferRequest
 *
 * @property int $id
 * @property int $mod_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Mod $mod
 * @property-read \App\Models\User $user
 * @property int|null $keep_owner_level
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest whereKeepOwnerLevel($value)
 */
class TransferRequest extends Model
{
    use HasFactory;

    protected $with = ['user'];

    protected $fillable = [
        'keep_owner_level',
    ];
    
    public function getMorphClass(): string {
        return 'transfer_request';
    }

    public function mod()
    {
        return $this->belongsTo(Mod::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
