<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Storage;

class CheckAvatarThumbs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mws:check-avatar-thumbs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs through each user to check if they have a thumbnail, if they do it sets the avatar_has_thumb to true, otherwise false.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::chunk(1000, function(Collection $users) {
            foreach ($users as $user) {
                if (!str_contains($user->avatar, 'https://')) {
                    $user->update([
                        'avatar_has_thumb' => Storage::has("users/images/thumbnail_{$user->avatar}")
                    ]);
                } else {
                    $user->update([
                        'avatar_has_thumb' => false
                    ]);
                }
            }
        });
    }
}
