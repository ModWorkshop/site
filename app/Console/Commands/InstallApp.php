<?php

namespace App\Console\Commands;

use Artisan;
use Illuminate\Console\Command;

class InstallApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mws:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A single and simple command to install the site. It runs the migrations and seeds the core data the site needs to function';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        echo "Let's rock\n";
        $this->call('migrate'); //Runs the migration
        //$this->call('db:seed --class=RoleSeeder');
        //$this->call('db:seed --class=UserSeeder');
        echo 'Done!';
        return 0;
    }
}
