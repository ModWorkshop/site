<?php

namespace App\Console;

use App\Console\Commands\GenerateSitemap;
use App\Jobs\CalculateModCounts;
use App\Jobs\CalculatePopularity;
use App\Jobs\CalculateThreadComments;
use App\Jobs\DeleteUnverifiedUsers;
use App\Jobs\RemoveExpiredRequests;
use App\Jobs\TryActivatingUsers;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        $schedule->job(new CalculatePopularity)->everyThirtyMinutes();
        $schedule->job(new TryActivatingUsers)->everyTwoHours();
        $schedule->job(new DeleteUnverifiedUsers)->everyTwoHours();
        $schedule->job(new CalculateThreadComments)->everyTwoHours();
        $schedule->job(new RemoveExpiredRequests)->everyTwoHours();
        $schedule->job(new GenerateSitemap)->everyThirtyMinutes();

        if (env('TELESCOPE_ENABLED')) {
            $schedule->command('telescope:prune')->everyTwoHours();
        }
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
