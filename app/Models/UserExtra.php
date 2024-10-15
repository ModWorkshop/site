<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
 * @method static Builder|UserExtra newModelQuery()
 * @method static Builder|UserExtra newQuery()
 * @method static Builder|UserExtra query()
 * @method static Builder|UserExtra whereBanner($value)
 * @method static Builder|UserExtra whereBio($value)
 * @method static Builder|UserExtra whereCreatedAt($value)
 * @method static Builder|UserExtra whereCustomTitle($value)
 * @method static Builder|UserExtra whereId($value)
 * @method static Builder|UserExtra whereLastOnline($value)
 * @method static Builder|UserExtra wherePrivateProfile($value)
 * @method static Builder|UserExtra whereUpdatedAt($value)
 * @method static Builder|UserExtra whereUserId($value)
 * @method static Builder|UserExtra whereLastOnlineAt($value)
 * @property Carbon|null $last_online
 * @property string|null $donation_url
 * @method static Builder|UserExtra whereDonationUrl($value)
 * @property string $default_mods_view
 * @property string $default_mods_sort
 * @property bool $home_show_last_games
 * @property bool $home_show_mods
 * @property bool $home_show_threads
 * @property bool $game_show_mods
 * @property bool $game_show_threads
 * @method static Builder|UserExtra whereDefaultModsSort($value)
 * @method static Builder|UserExtra whereDefaultModsView($value)
 * @method static Builder|UserExtra whereGameShowMods($value)
 * @method static Builder|UserExtra whereGameShowThreads($value)
 * @method static Builder|UserExtra whereHomeShowLastGames($value)
 * @method static Builder|UserExtra whereHomeShowMods($value)
 * @method static Builder|UserExtra whereHomeShowThreads($value)
 * @property bool $auto_subscribe_to_mod
 * @property bool $auto_subscribe_to_thread
 * @property string $background
 * @property float $background_opacity
 * @method static Builder|UserExtra whereAutoSubscribeToMod($value)
 * @method static Builder|UserExtra whereAutoSubscribeToThread($value)
 * @method static Builder|UserExtra whereBackground($value)
 * @method static Builder|UserExtra whereBackgroundOpacity($value)
 * @mixin Eloquent
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
