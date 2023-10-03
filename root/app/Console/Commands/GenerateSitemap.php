<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Game;
use App\Models\Mod;
use App\Models\Thread;
use App\Models\User;
use App\Services\ModService;
use App\Services\ThreadService;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\SitemapIndex;

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', '2G');

        $frontendApi = env('FRONTEND_URL').'/';

        Sitemap::create()->add(ModService::viewFilters(Mod::with([]))->get())->writeToFile('./public/mods_sitemap.xml');
        Sitemap::create()->add(ThreadService::filters(Thread::with([]))->get())->writeToFile('./public/threads_sitemap.xml');
        Sitemap::create()->add(Game::with([])->get())->add(Category::with(['game'])->get())->add(Forum::with(['game'])->get())->writeToFile('./public/games_sitemap.xml');

        $pubUsers = User::wherePrivateProfile(false)->with([])->select(['id', 'updated_at']);
        $userI = 0;
        $pubUsers->chunk(50000, function($users) use (&$userI) {
            $userI++;
            Sitemap::create()->add($users)->writeToFile('./public/users_'.$userI.'_sitemap.xml');
        });

        $index = SitemapIndex::create()->add($frontendApi.'mods_sitemap.xml')->add($frontendApi.'games_sitemap.xml')->add($frontendApi.'threads_sitemap.xml');

        for ($i = 1; $i <= $userI; $i++) {
            $index->add($frontendApi.'users_'.$i.'_sitemap.xml');
        }

        $index->writeToFile('./public/sitemap_index.xml');
    }
}
