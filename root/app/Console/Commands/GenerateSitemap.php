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

        Sitemap::create()->add(ModService::viewFilters(Mod::query())->get())->writeToFile('./public/mods_sitemap.xml');
        Sitemap::create()->add(ThreadService::filters(Thread::query())->get())->writeToFile('./public/threads_sitemap.xml');

        Sitemap::create()->add(Game::all())->add(Category::all())->add(Forum::all())->writeToFile('./public/games_sitemap.xml');

        SitemapIndex::create()
            ->add(env('FRONTEND_URL').'/mods_sitemap.xml')
            ->add(env('FRONTEND_URL').'/games_sitemap.xml')
            ->add(env('FRONTEND_URL').'/threads_sitemap.xml')
            ->writeToFile('./public/sitemap.xml');
    }
}
