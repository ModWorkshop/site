<?php

namespace App\Console\Commands;

use App;
use App\Models\Category;
use App\Models\Forum;
use App\Models\Game;
use App\Models\Mod;
use App\Models\Tag;
use App\Models\Thread;
use App\Models\User;
use App\Services\ModService;
use App\Services\ThreadService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\SitemapIndex;
use Symfony\Component\Console\Helper\ProgressBar;

class GenerateSitemap extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    public function progress($str, $total)
    {
        ProgressBar::setFormatDefinition('with_message', ' %current%/%max% -- %message%');

        $this->info($str);
        $bar = $this->output->createProgressBar($total);
        $bar->setFormat('with_message');
        $this->newLine();
        return $bar;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', '2G');

        $sitemapIndex = App::make ("sitemap");
        $url = env('FRONTEND_URL').'/';
        $startT = time();
        $progress = $this->progress('Generating...', 4);

        // Mods
        $modsSitemap = App::make('sitemap');
        $progress->setMessage('Generating mods sitemap');
        foreach (ModService::viewFilters(Mod::with([]))->get() as $mod) {
            $modsSitemap->add($url.'mod/'.$mod->id, Carbon::create($mod->bumped_at), 0.8, 'weekly');
        }
        $modsSitemap->store('xml', 'mods_sitemap');
        $sitemapIndex->addSitemap($url.'mods_sitemap.xml');
        $progress->advance();
        //////////////

        // Threads
        $threadsSitemap = App::make('sitemap');
        $progress->setMessage('Generating threads sitemap');
        foreach (ThreadService::filters(Thread::with([]))->get() as $thread) {
            $threadsSitemap->add($url.'thread/'.$thread->id, Carbon::create($thread->bumped_at), 0.6, 'weekly');
        }
        $threadsSitemap->store('xml', 'threads_sitemap');
        $sitemapIndex->addSitemap($url.'threads_sitemap.xml');
        $progress->advance();
        //////////////

        // Game + Related
        $gamesSitemap = App::make('sitemap');
        $progress->setMessage('Generating games + related sitemap');
        foreach (Game::with([])->get() as $game) {
            $gameUrl = $url.'g/'.$game->short_name;
            $gamesSitemap->add($gameUrl, Carbon::create($game->last_date), 0.9, 'hourly');
            foreach ($game->categories as $cat) {
                $gamesSitemap->add($gameUrl.'?category='.$cat->id.'&category-name='.$cat->name, Carbon::create($game->last_date), 0.7, 'daily');
            }
            foreach ($game->tags as $tag) {
                $gamesSitemap->add($gameUrl.'?selected-tags='.$tag->id, Carbon::create($game->last_date), 0.7, 'daily');
            }
            foreach (Tag::whereNull('game_id')->get() as $tag) {
                $gamesSitemap->add($gameUrl.'?selected-tags='.$tag->id, Carbon::create($game->last_date), 0.7, 'daily');
            }
            $gamesSitemap->add($gameUrl.'/forum', Carbon::create($game->forum->updated_at), 0.7, 'daily');
        }
        $gamesSitemap->store('xml', 'games_sitemap');
        $sitemapIndex->addSitemap($url.'games_sitemap.xml');
        $progress->advance();
        //////////////

        // Users
        $progress->setMessage('Generating users sitemap');
        $pubUsers = User::wherePrivateProfile(false)->with([])->select(['id', 'updated_at']);
        $userI = 0;
        $pubUsers->chunk(50000, function($users) use (&$userI, $url, $sitemapIndex) {
            $userI++;

            $sitemap = App::make('sitemap');

            foreach ($users as $user) {
                $sitemap->add($url.'user/'.$user->id, Carbon::create($user->updated_at), 0.5, 'weekly');
            }

            $sitemap->store('xml', 'users_'.$userI.'_sitemap');
            $sitemapIndex->addSitemap($url.'users_'.$userI.'_sitemap.xml');
        });
        $progress->advance();
        //////////////

        $sitemapIndex->store('sitemapindex','sitemap_index');
        $this->newLine();
        $this->info('Done. Took: '.time()-$startT.' seconds');
    }
}
