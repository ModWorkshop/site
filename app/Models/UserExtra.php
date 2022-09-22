<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserExtra
 *
 * @property int $id
 * @property string $banner
 * @property string $bio
 * @property bool $private_profile
 * @property string $custom_title
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereCustomTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereLastOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra wherePrivateProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereLastOnlineAt($value)
 * @property Carbon|null $last_online
 * @property string|null $donation_url
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereDonationUrl($value)
 */
class UserExtra extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $casts = [
    ];

    public function getMorphClass(): string {
        return 'user_extra';
    }
}
