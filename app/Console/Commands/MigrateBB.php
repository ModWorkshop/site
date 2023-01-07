<?php

namespace App\Console\Commands;

use App\Models\Ban;
use App\Models\Category;
use App\Models\File;
use App\Models\Forum;
use App\Models\Game;
use App\Models\Image;
use App\Models\InstructsTemplate;
use App\Models\Link;
use App\Models\Mod;
use App\Models\ModMember;
use App\Models\Tag;
use App\Models\Taggable;
use App\Models\User;
use App\Models\UserCase;
use App\Services\Utils;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Str;
const NEW_TAG_TYPES = [
    1 => 'info',
    2 => 'warning',
    3 => 'danger',
];

class MigrateBB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-bb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private Connection $con;

    public function progress($str, $total)
    {
        $this->info($str);
        return $this->output->createProgressBar($total);
    }

    public function handleUnixDate($unix)
    {
        if ($unix == '---') {
            $unix = null;
        }

        return $unix ? Carbon::parse($unix) : null;
    }

    public function resetAutoIncrement($tableName)
    {
        DB::select("SELECT SETVAL(pg_get_serial_sequence({$tableName}, 'id'), (SELECT MAX(id) FROM {$tableName}));");
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        #mbb_attachments 
        # Current forum does not have file uploading, backup this with all of the files to somewhere.
        # TODO: backup attachments
        #mbb_banfilters 
        # TODO: implement IP bans
        # TODO: think about porting the forum data (mbb_forums)
        # TODO: think about porting mbb_mws_cases
        # TODO: port following & subs tables

        $this->info("Converting old-ass data to new-and-improved data!");
        ini_set("memory_limit", 1073741824);

        $this->con = DB::connection('mysql_migration');
        //$this->handleUsers();
        //$this->handleBans();
        //$this->handleCategoriesGames();
        //$this->handleTags();
        //$this->handleInstructionsTemplates();
        //$this->handleMods();



        // $downloadsLog = implementThisLmao();
        // foreach ($downloadsLog as $download) {
        //     ModDownload::create([
        //         'user_id' => $download->uid, # Changed
        //         'mod_id' => $download->did, # Changed
        //         'ip_address' => $download->ipaddress, # Changed
        //         'updated_at' => $this->handleUnixDate($like->date), # Changed
        //         'created_at' => $this->handleUnixDate($like->date)
        //     ]);
        // }

        // $viewsLog = implementThisLmao();
        // foreach ($viewsLog as $view) {
        //     ModView::create([
        //         'user_id' => $view->uid, # Changed
        //         'mod_id' => $view->did, # Changed
        //         'ip_address' => $view->ipaddress, # Changed
        //     ]);
        // }

        // $logs = implementThisLmao();
        // const NEW_TYPES = [
        //     '6' => 'view',
        //     '5' => 'down',
        //     '2' => 'like'
        // ];
        // foreach ($logs as $log) {
        //     PopularityLog::create([
        //         'id' => $log->lid, # Changed
        //         'user_id' => $log->uid,
        //         'ip_address' => $log->ipaddress,
        //         'mod_id' => $log->did,
        //         'updated_at' => $this->handleUnixDate($log->date),
        //         'type' => NEW_TYPES[$log->type] # Really changed
        //     ]);
        // }

        // $likes = implementThisLmao();
        // foreach ($likes as $like) {
        //     # Removed IP (redundant, only users can like mods!)
        //     # Removed type (redundant)
        //     ModLike::create([
        //         'mod_id' => $like->did,
        //         'user_id' => $like->uid,
        //         'updated_at' => $this->handleUnixDate($like->stamp), # Handle date
        //         'created_at' => $this->handleUnixDate($like->stamp)
        //     ]);
        // }

        // $comments = implementThisLmao();
        // foreach ($comments as $comment) {
        //     # Removed cid (unused)
        //     Comment::create([
        //         'user_id' => $comment->uid, # Changed,
        //         'commentable_type' => 'mod', # Migrated data is ONLY mods, we may migrate mybb threads
        //         'commentable_id' => $comment->did, # Changed
        //         'content' => $comment->comment, # Changed
        //         'edited_at' => $this->handleUnixDate($comment->date_edited), #Changed, handle date
        //         'created_at' => $this->handleUnixDate($comment->date), # Changed
        //         'pinned' => $comment->pinned == '1',
        //         'reply_to' => $comment->replyid
        //     ]);
        // }
        return Command::SUCCESS;
    }

    public function handleUsers()
    {
        $bar = $this->progress('Converting users', $this->con->table('users')->count());
        $this->con->table('users')->orderBy('uid')->chunk(1000, function($users) use ($bar) {
            foreach ($users as $user) {
                if ($user->uid == 1) {
                    continue; // ModWorkshop account can be ignored.
                }
                $bar->advance();
                $userPrefs = $this->con->table('mws_user_prefs')->where('uid', $user->uid)->first(['banner', 'private_profile']);
                $userFields = $this->con->table('userfields')->where('ufid', $user->uid)->first('fid2'); # uid is ufid for some reason

                User::forceCreate([
                    'id' => $user->uid,
                    'name' => $user->username,
                    'avatar' => $user->avatar, // TODO: do we want to store user images in images table, therefore making this column legacy?
                    'custom_color' => Str::limit($user->customcolor, 6, ''),
                    'unique_name' => Utils::getUniqueName($user->username),
                    'custom_title' => $user->usertitle, # Changed
                    'created_at' => $user->regdate, # Changed
                    'last_online' => $user->lastvisit, # Changed
                    'invisible' => $user->invisible == 1,
                    'banner' => $userPrefs->banner,
                    'private_profile' => $userPrefs->private_profile,
                    'bio' =>  $userFields->fid2 # Lol
                ]);
            }
        });

        $this->resetAutoIncrement('users');
    }

    public function handleBans()
    {
        $bar = $this->progress('Converting bans', $this->con->table('banned')->count());
        $this->con->table('banned')->orderBy('uid')->chunk(1000, function($bans) use ($bar) {
            foreach ($bans as $ban) {
                $bar->advance();
                $case = UserCase::create([
                    'user_id' => $ban->uid, # Changed
                    'mod_user_id' => $ban->admin, # Changed
                    'warning' => false, #Figure
                    'reason' => $ban->reason,
                    'created_at' => $this->handleUnixDate($ban->dateline),
                    'updated_at' => $this->handleUnixDate($ban->dateline),
                    'expire_date' => $this->handleUnixDate($ban->bantime),
                ]);
                Ban::create([
                    'user_id' => $ban->uid,
                    'case_id' => $case->id
                ]);
            }
        });

        $bar->finish();
    }

    public function handleCategoriesGames()
    {
        $bar = $this->progress('Converting games and categories', $this->con->table('mydownloads_categories')->count());
        $cats = $this->con->table('mydownloads_categories')->get();
        foreach ($cats as $cat) {
            if (!$cat->parent) { # Game
                Game::create([
                    'id' => $cat->cid,
                    'name' => $cat->name,
                    'thumbnail' => $cat->background,
                    'banner' => $cat->banner,
                    'buttons' => $cat->buttons,
                    'short_name' => empty($cat->short_name) ? Str::snake($cat->name) : $cat->short_name,
                    'disporder' => $cat->disporder,
                    'last_date' => $this->handleUnixDate($cat->last_date),
                    'webhook_url' => $cat->webhook_url,
                    'mods_count' => $cat->downloads
                ]);
            }
        }
        foreach ($cats as $cat) {
            $bar->advance();
            if ($cat->parent) { # Category
                Category::create([
                    'id' => $cat->cid,
                    'name' => $cat->name,
                    'desc' => $cat->description,
                    'game_id' => $cat->root,
                    'thumbnail' => $cat->background,
                    'disporder' => $cat->disporder,
                    'last_date' => $this->handleUnixDate($cat->last_date),
                    'webhook_url' => $cat->webhook_url,
                ]);
            }
        }
        foreach ($cats as $cat) {
            if ($cat->parent && Category::where('id', $cat->parent)->exists()) { # Category
                Category::where('id', $cat->cid)->update([
                    'parent_id' => $cat->parent,
                ]);
            }
        }
        $bar->finish();

        $this->resetAutoIncrement('categories');
        $this->resetAutoIncrement('games');
    }

    public function handleTags()
    {
        $bar = $this->progress('Converting tags', $this->con->table('mydownloads_tags')->count());
        $tags = $this->con->table('mydownloads_tags')->get();
        $multipleGameTags = [];
        foreach ($tags as $tag) {
            $bar->advance();
            $games = explode(',', $tag->categories);
            $gameId = $games[0] ?? null;
            if (empty($gameId)) {
                $gameId = null;
            };
            Tag::create([
                'id' => $tag->tid, 
                'name' => $tag->tag,
                'color' => $tag->color,
                'notice' => $tag->notice,
                'notice_localized' => $tag->notice_localized == 1,
                'notice_type' => NEW_TAG_TYPES[$tag->notice_type],
                'type' => 'mod',
                'game_id' => $gameId
            ]);

            if (count($games) > 1) { // Tag is used by multiple games (and is not global), oh well duplicate it.
                $multipleGameTags[] = $tag;
            }
        }

        $this->resetAutoIncrement('tags');

        foreach ($multipleGameTags as $tag) {
            $games = explode(',', $tag->categories);
            for($i = 1; $i < count($games); $i++) {
                Tag::create([
                    'name' => $tag->tag,
                    'color' => $tag->color,
                    'notice' => $tag->notice,
                    'notice_localized' => $tag->notice_localized == 1,
                    'notice_type' => NEW_TAG_TYPES[$tag->notice_type],
                    'type' => 'mod',
                    'game_id' => $games[$i]
                ]);
            }
        }

        $bar->finish();
    }

    public function handleInstructionsTemplates()
    {
        $bar = $this->progress('Converting mods', $this->con->table('mydownloads_tags')->count());
        $templates = $this->con->table('mods_instructions_templates')->get();
        foreach ($templates as $template) {
            $bar->advance();
            $games = explode(',', $template->categories);
            InstructsTemplate::create([
                'id' => $template->instid, # Changed
                'name' => $template->name,
                'instructions' => $template->instructions,
                'localized' => $template->localized,
                'game_id' => $games[0], # Make sure only one game is defined per v3's standards / Changed
            ]);
        }
        $bar->finish();
        $this->resetAutoIncrement('instructs_templates');
    }

    public function handleMods()
    {
        $bar = $this->progress('Converting mods', $this->con->table('mydownloads_tags')->count());
        $this->con->table('mydownloads_downloads')->orderBy('uid')->chunk(1000, function($mods) use ($bar) {
            foreach ($mods as $mod) {
                $bar->advance();
                # Score is recalculated.
                # Invited removed due to underuse.
    
                $newMod = Mod::create([
                    'id' => $mod->did,
                    'category_id' => $mod->cid, # Changed
                    'game_id' => $mod->root, # Changed
                    'name' => $mod->name,
                    'desc' => $mod->description, # Changed
                    'instructions' => $mod->instructions,
                    'short_desc' => $mod->short_description, # Changed
                    'changelog' => $mod->changelog,
                    'visibility' => $mod->hidden, # Changed, needs figuring out
                    'depends_on' => $mod->depends_on, # Figure out
                    'instructs_template_id' => $mod->instid, # Changed
                    'downloads' => $mod->downloads,
                    'likes' => $mod->likes,
                    'views' => $mod->view,
                    'user_id' => $mod->submitter_uid, # Changed
                    'license' => $mod->license,
                    'version' => $mod->version,
                    'date' => $this->handleUnixDate($mod->bumped_at), # Changed
                    'created_at' => $this->handleUnixDate($mod->pub_date), # New #Handle dates
                    'updated_at' => $this->handleUnixDate($mod->bumped_at), # New
                    'published_at' => $this->handleUnixDate($mod->pub_date), # Changed
                    'donation' => $mod->receiver_email, # Changed
                    'suspended' => $mod->suspended_status == 1, # Changed into a boolean
                    'comments_disabled' => $mod->comments_disabled == 1, # Changed into a boolean
                    'has_download' => false, # Manually recalculate using v3 conditions :)
                    'approved' => $mod->file_status != 2,
                ]);

                $images = $this->con->table('mws_images')->where('did', $mod->id)->get();
                $foundBanner = false;
                foreach ($images as $image) {
                    Image::create([
                        'id' => $image->id,
                        'mod_id' => $image->did,
                        'user_id' => $image->uid,
                        'file' => $image->file,
                        'type' => $image->type,
                        'has_thumb' => $image->has_thumb == 1,
                        'size' => $image->size,
                        'created_at' => $this->handleUnixDate($image->date),
                        'updated_at' => $this->handleUnixDate($image->date),
                    ]);

                    if ('https://modworkshop.net/mydownloads/previews/'.$image->file == $mod->banner) {
                        $newMod->update([
                            'banner_id' => $image->id
                        ]);
                        $foundBanner = true;
                    }
                }

                $files = $this->con->table('mws_mydownloads_files')->where('did', $mod->id)->get();
                $firstModFileId = null;
                foreach ($files as $file) {
                    $firstModFileId ??= $file->id;
                    File::create([
                        'id' => $file->fid,
                        'mod_id' => $file->did,
                        'user_id' => $file->uid,
                        'file' => $file->file,
                        'desc' => $file->description,
                        'image_id' => $file->image, #TODO: Deal with this, has also thumbnail column
                        'type' => $file->filetype,
                        'size' => $file->filesize,
                        'created_at' => $this->handleUnixDate($file->date),
                        'updated_at' => $this->handleUnixDate($file->date),
                    ]);
                }

                $link = null;
                if (isset($newMod->url)) {
                    $link = Link::create([
                        'user_id' => $newMod->user_id,
                        'mod_id' => $newMod->id,
                        'name' => $newMod->url,
                        'url' => $newMod->url,
                    ]);
                }

                $newMod->update([
                    'thumbnail_id' => $mod->thumbnail_id,
                    'legacy_banner_url' => $foundBanner ? null : $mod->banner,
                    'download_type' => isset($newMod->url) ? 'link' : 'file',
                    'download_id' => isset($newMod->url) ? $link->id : $firstModFileId,
                ]);

                foreach (explode(',', $mod->tags) as $id) {
                    Taggable::create([
                        'tag_id' => $id,
                        'taggable_type' => 'mod',
                        'taggable_id' => $newMod->id,
                    ]);
                }
    
                foreach (explode(',', $mod->collaborators) as $id) {
                    ModMember::create([
                        'mod_id' => $newMod->id,
                        'level' => 'collaborator',
                        'accepted' => true
                    ]);
                }
    
                foreach (explode(',', $mod->invited) as $id) {
                    ModMember::create([
                        'mod_id' => $newMod->id,
                        'level' => 'viewer',
                        'accepted' => true
                    ]);
                }
            }
        });

        $bar->finish();
        $this->resetAutoIncrement('mods');
        $this->resetAutoIncrement('files');
        $this->resetAutoIncrement('images');
    }
}
