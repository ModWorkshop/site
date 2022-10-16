<?php

namespace App\Console;

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
        $schedule->call(function() {
            Log::info('Calculating Popularity...');

            PopularityLog::whereDate('updated_at', '>', Carbon::now()->addYear(1))->delete();

            function getScores(Carbon $date) {
                $scores = [];
                PopularityLog::select('mod_id', DB::raw("SUM(
                    CASE 
                        WHEN type = 'view' THEN 1
                        WHEN type = 'down' THEN 2
                        WHEN type = 'like' THEN 5 
                    END) AS score"))
                ->whereDate('updated_at', '<', $date)
                ->groupBy('mod_id')
                ->orderBy('mod_id')
                ->chunk(500, function($logs)  use (&$scores) {
                    foreach ($logs as $log) {
                        $scores[$log->mod_id] = $log->score;
                    }
                });

                return $scores;
            }

            $scoresMonthly = getScores(Carbon::now()->addMonth(1));
            $scoresWeekly = getScores(Carbon::now()->addWeek(1));
            $scoresDaily = getScores(Carbon::now()->addDay(1));

            Mod::setEagerLoads([])->chunk(1000, function($mods) use (&$scoresDaily, &$scoresWeekly, &$scoresMonthly) {
                foreach ($mods as $mod) {
                    $weeks = Carbon::now()->diffInWeeks($mod->bumped_at);

                    $score = 0;
                    $dailyScore = 0;
                    $weeklyScore = 0;

                    if (isset($scoresDaily[$mod->id])) {
                        $dailyScore = log(max(1, $scoresDaily[$mod->id]));
                        if($weeks > 1) {
                            $dailyScore *= exp(-0.03*$weeks*$weeks);
                        }
                    }

                    if (isset($scoresWeekly[$mod->id])) {
                        $weeklyScore = log(max(1, $scoresWeekly[$mod->id]));
                        if($weeks > 1) {
                            $weeklyScore *= exp(-0.03*$weeks*$weeks);
                        }
                    }

                    if (isset($scoresMonthly[$mod->id])) {
                        $score = log(max(1, $scoresMonthly[$mod->id]));
                        if($weeks > 1) {
                            $score *= exp(-0.03*$weeks*$weeks);
                        }
                    }

                    if(abs($score - $mod->score) > 0.001 || abs($dailyScore - $mod->daily_score) > 0.001 || abs($weeklyScore - $mod->weekly_score) > 0.001) {
                        $mod->update([
                            'daily_score' => $dailyScore,
                            'weekly_score' => $weeklyScore,
                            'score' => $score,
                        ]);
                    }

                    unset($scoresDaily[$mod->id]);
                    unset($scoresWeekly[$mod->id]);
                    unset($scoresMonthly[$mod->id]);
                    unset($weeks);
                    unset($mod);
                }
            });

            unset($scores); //The log can be HUGE so let's do our server a favor and unset it after we're done.

            Log::info('Done Calculating Popularity');
        })->everyMinute();
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
