<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use DB;
use Hash;
use Illuminate\Console\Command;
use Storage;

class InitialSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'initial-setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the site all in one command!';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("ModWorkshop Installation");
        $this->info('Running migrations...');
        $this->call('migrate');
        $this->info('Creating necessary folders in storage...');
        Storage::makeDirectory('mods/files');
        Storage::makeDirectory('mods/images');
        Storage::makeDirectory('users/avatars');
        Storage::makeDirectory('users/banners');
        $this->info('Running storage:link...');
        $this->call('storage:link');
        $this->info('Running migrations...Done!');
        $this->info('Running Necessary Seeders...');
        $this->call('db:seed', ['class' => 'RolePermissionsSeeder']);
        $this->info('Running Necessary Seeders...Done!');

        $oldUser = User::find(1);
        if (!isset($oldUser) || $this->confirm('Super-user already exists, do you wish to override it?')) {
            $this->info('In order to access the super-admin account you will need to provide an email address and password.');
            $email = $this->ask('Email Address:');
            $password = $this->secret('Password:');
            $oldUser?->delete(); # Make sure old admin account is deleted
            User::forceCreate([
                'id' => 1,
                'name' => 'ModWorkshop',
                'unique_name' => '',
                'email' => $email,
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make($password),
            ]);
            DB::select("SELECT SETVAL(pg_get_serial_sequence('users', 'id'), (SELECT MAX(id) FROM users));");
            $this->info('Created sueper-admin account!');
        }

        $this->info('The site is ready to use! Have fun :)');
        $this->info('Run it via php artisan serve.');

        return Command::SUCCESS;
    }
}
