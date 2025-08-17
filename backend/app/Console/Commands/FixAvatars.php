<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Storage;

class FixAvatars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mws:fix-avatars';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs through each user to check if they have a valid avatar. If not, nullifies the avatar.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::whereNot('avatar', '')->whereNot('avatar', 'LIKE', '%https://%')->chunk(1000, function(Collection $users) {
            foreach ($users as $user) {
                if (!Storage::exists("users/images/{$user->avatar}")) {
                    $this->info('Avatar ' . $user->avatar . 'of user '. $user->id . ' was missing and will be cleared');
                    $user->update([
                        'avatar' => '',
                        'avatar_has_thumb' => false
                    ]);
                }
            }
        });
    }
}
