<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\TransferRequest
 *
 * @property int $id
 * @property int $mod_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|TransferRequest newModelQuery()
 * @method static Builder|TransferRequest newQuery()
 * @method static Builder|TransferRequest query()
 * @method static Builder|TransferRequest whereCreatedAt($value)
 * @method static Builder|TransferRequest whereId($value)
 * @method static Builder|TransferRequest whereModId($value)
 * @method static Builder|TransferRequest whereUpdatedAt($value)
 * @method static Builder|TransferRequest whereUserId($value)
 * @property-read Mod $mod
 * @property-read User $user
 * @property int|null $keep_owner_level
 * @method static Builder|TransferRequest whereKeepOwnerLevel($value)
 * @mixin Eloquent
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

    public static function booted()
    {
        static::deleting(function(TransferRequest $request) {
            Notification::deleteRelated($request->mod, 'transfer_ownership');
        });
    }
}
