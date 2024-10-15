<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use DB;
use Hash;
use Illuminate\Console\Command;
use Schema;
use Storage;

class InstallMWS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mws:install {--auto} {--force}';

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
        $auto = $this->option('auto');
        $force = $this->option('force');

        if (Schema::hasTable('users') && $auto && !$force) {
            $this->info("Skipping...");
            return;
        }

        $this->info('Running migrations...');
        $this->call('migrate', ['--force' => true]);
        $this->info('Creating necessary folders in storage...');
        Storage::disk('local')->makeDirectory('mods/files');
        Storage::disk('local')->makeDirectory('mods/images');
        Storage::disk('local')->makeDirectory('users/images');
        $this->info('Running storage:link...');
        $this->call('storage:link');
        $this->info('Running migrations...Done!');
        $this->info('Running Necessary Seeders...');
        $this->call('db:seed', ['class' => 'RolePermissionsSeeder']);
        $this->info('Running Necessary Seeders...Done!');

        $oldUser = User::find(1);
        if ($force || !isset($oldUser) || $this->confirm('Super-user already exists, do you wish to override it?')) {
            $this->info('In order to access the super-admin account you will need to provide an email address and password.');
            $email = $auto ? env('ADMIN_EMAIL') : $this->ask('Email Address:');
            $password = $auto ? env('ADMIN_PASSWORD') : $this->secret('Password:');
            $oldUser?->delete(); # Make sure old admin account is deleted
            User::forceCreate([
                'id' => 1,
                'name' => 'ModWorkshop',
                'unique_name' => '',
                'activated' => true,
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

