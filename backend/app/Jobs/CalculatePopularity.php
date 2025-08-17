<?php

namespace App\Jobs;

use App\Models\Mod;
use App\Models\PopularityLog;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use function set_time_limit;

class CalculatePopularity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        set_time_limit(600);
        ini_set('memory_limit', '1G');

        Log::info('Calculating Popularity...');

        PopularityLog::whereDate('updated_at', '>', Carbon::now()->addYear(1))->delete();

        $getScores = function(Carbon $date) {
            $scores = [];

            $results = DB::select("
                SELECT
                    pl.mod_id,
                    1000 * LOG(1 + SUM(
                        CASE
                            WHEN pl.type = 'view' THEN 1
                            WHEN pl.type = 'down' THEN 4
                            WHEN pl.type = 'like' THEN 6
                            ELSE 0
                        END
                    )) / EXP(0.01 * DATE_PART('day', now() - m.bumped_at)) AS score
                FROM popularity_logs pl
                JOIN mods m ON pl.mod_id = m.id
                WHERE pl.updated_at > ?
                GROUP BY pl.mod_id, m.bumped_at
                ORDER BY score DESC
            ", [$date->toDateTimeString()]);

            foreach ($results as $row) {
                $scores[$row->mod_id] = (float)$row->score;
            }

            return $scores;
        };

        Log::info('Getting monthly scores...');
        $scoresMonthly = $getScores(Carbon::now()->subMonth());
        Log::info('Getting weekly scores...');
        $scoresWeekly = $getScores(Carbon::now()->subWeek());
        Log::info('Getting daily scores...');
        $scoresDaily = $getScores(Carbon::now()->subDay());

        Log::info('Calculating scores of all mods...');
        
        Mod::setEagerLoads([])->chunkById(1000, function($mods) use (&$scoresDaily, &$scoresWeekly, &$scoresMonthly, &$bulkUpdates) {
            foreach ($mods as $mod) {
                $score = $scoresMonthly[$mod->id] ?? 0;
                $dailyScore = $scoresDaily[$mod->id] ?? 0;
                $weeklyScore = $scoresWeekly[$mod->id] ?? 0;

                
                if(abs($score - $mod->score) > 0.00001 || abs($dailyScore - $mod->daily_score) > 0.00001 || abs($weeklyScore - $mod->weekly_score) > 0.00001) {
                    $mod->timestamps = false;
                    $mod->daily_score = $dailyScore;
                    $mod->weekly_score = $weeklyScore;
                    $mod->score = $score;
                    $mod->save();
                }

                unset($scoresDaily[$mod->id]);
                unset($scoresWeekly[$mod->id]);
                unset($scoresMonthly[$mod->id]);
            }
        }, 'id');

        unset($scores); //The log can be HUGE so let's do our server a favor and unset it after we're done.

        Log::info('Done Calculating Popularity');
    }
}
