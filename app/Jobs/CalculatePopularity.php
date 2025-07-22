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
            PopularityLog::select('mod_id', DB::raw("
                1000 * LOG(1 + SUM(
                CASE
                    WHEN popularity_logs.type = 'view' THEN 1
                    WHEN popularity_logs.type = 'down' THEN 4
                    WHEN popularity_logs.type = 'like' THEN 6
                END)) / EXP(0.005 * DATE_PART('day', now() - mods.bumped_at)) AS score
            "))
            ->whereDate('mods.updated_at', '>', $date)
            ->groupBy('mod_id', 'mods.bumped_at')
            ->orderBy('mod_id')
            ->join('mods', 'popularity_logs.mod_id', '=', 'mods.id')
            ->chunk(10000, function($logs)  use (&$scores) {
                foreach ($logs as $log) {
                    $scores[$log->mod_id] = $log->score;
                }
            });

            return $scores;
        };

        Log::info('Getting monthly scores...');
        $scoresMonthly = $getScores(Carbon::now()->subMonth());
        Log::info('Getting weekly scores...');
        $scoresWeekly = $getScores(Carbon::now()->subWeek());
        Log::info('Getting daily scores...');
        $scoresDaily = $getScores(Carbon::now()->subDay());

        Log::info('Calculating scores of all mods...');
        $bulkUpdates = [];
        
        Mod::setEagerLoads([])->chunkById(1000, function($mods) use (&$scoresDaily, &$scoresWeekly, &$scoresMonthly, &$bulkUpdates) {
            foreach ($mods as $mod) {
                $score = $scoresMonthly[$mod->id] ?? 0;
                $dailyScore = $scoresDaily[$mod->id] ?? 0;
                $weeklyScore = $scoresWeekly[$mod->id] ?? 0;

                
                if(abs($score - $mod->score) > 0.00001 || abs($dailyScore - $mod->daily_score) > 0.00001 || abs($weeklyScore - $mod->weekly_score) > 0.00001) {
                    $bulkUpdates[] = [
                        'id' => $mod->id,
                        'daily_score' => $dailyScore,
                        'weekly_score' => $weeklyScore,
                        'score' => $score
                    ];
                }

                unset($scoresDaily[$mod->id]);
                unset($scoresWeekly[$mod->id]);
                unset($scoresMonthly[$mod->id]);
            }
            
            // Perform bulk update for this chunk
            if (!empty($bulkUpdates)) {
                DB::table('mods')->update(
                    $bulkUpdates,
                    ['id'],
                    ['daily_score', 'weekly_score', 'score']
                );
                $bulkUpdates = []; // Clear for next chunk
            }
        }, 'id');

        unset($scores); //The log can be HUGE so let's do our server a favor and unset it after we're done.

        Log::info('Done Calculating Popularity');
    }
}
