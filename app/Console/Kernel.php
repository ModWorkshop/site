<?php

namespace App\Console;

use App\Jobs\CalculateModCounts;
use App\Jobs\CalculateModsCounts;
use App\Jobs\CalculatePopularity;
use App\Jobs\DeleteUnverifiedUsers;
use App\Jobs\TryActivatingUsers;
use App\Models\Mod;
use App\Models\PopularityLog;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new CalculatePopularity)->everyMinute();
        $schedule->job(new TryActivatingUsers)->everyHour();
        $schedule->job(new DeleteUnverifiedUsers)->everyMinute();
        $schedule->job(new CalculateModsCounts)->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
