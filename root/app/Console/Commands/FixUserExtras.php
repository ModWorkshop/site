<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class FixUserExtras extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mws:fix-user-extras';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes user extras. In case some user are missing them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::whereDoesntHave('extra')->chunk(1000, function(Collection $users) {
            foreach ($users as $user) {
                $user->extra()->create();
            }
        });
    }
}
