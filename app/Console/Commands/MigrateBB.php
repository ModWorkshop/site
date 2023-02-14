<?php

namespace App\Console\Commands;

use App\Models\Ban;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Dependency;
use App\Models\File;
use App\Models\FollowedGame;
use App\Models\FollowedMod;
use App\Models\FollowedUser;
use App\Models\ForumCategory;
use App\Models\Game;
use App\Models\Image;
use App\Models\InstructsTemplate;
use App\Models\Link;
use App\Models\Mod;
use App\Models\ModDownload;
use App\Models\ModLike;
use App\Models\ModMember;
use App\Models\ModView;
use App\Models\PopularityLog;
use App\Models\SocialLogin;
use App\Models\Tag;
use App\Models\Taggable;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Thread;
use App\Models\UserCase;
use App\Models\Visibility;
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

const NEW_LOG_TYPES = [
    '6' => 'view',
    '5' => 'down',
    '2' => 'like'
];

const NEW_VISIBILITY = [
    '0' => 'public',
    '1' => 'private',
    '3' => 'unlisted',
    '4' => 'private' # Was invite only
];

const STRIKES_TO_WARNINGS = [
    1 => 1,
    2 => 1,
    3 => 2
];

const NEW_SUB_TYPES = [
    1 => 'mod',
    2 => 'comment'
];

/**
 * After years of using MyBB this is the final nail to the coffin.
 * This is the file where we go from  early 2000s shitty code to something more modern.
 * This took me a lot of pain to make as it required me to really go through the whole database and see what we need.
 */

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

    private array $modIds = [];

    public function progress($str, $total)
    {
        $this->info($str);
        $bar = $this->output->createProgressBar($total);
        $this->newLine();
        return $bar;
    }

    // Now with support above 2038!!!!
    public function handleUnixDate($unix)
    {
        if ($unix == '---' || empty($unix)) {
            $unix = null;
        }

        return $unix ? Carbon::createFromTimestamp($unix) : null;
    }

    public function resetAutoIncrement($tableName)
    {
        DB::select("SELECT SETVAL(pg_get_serial_sequence('{$tableName}', 'id'), (SELECT MAX(id) FROM {$tableName}));");
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        #mbb_attachments 
        # Current forum does not have file uploading, backup the files to somewhere.
        #mbb_banfilters 
        # TODO: implement IP bans

        $this->info("Converting old-ass data to new-and-improved data!");
        ini_set("memory_limit", -1);
        set_time_limit(1000000000); // This can take A looong time.

        $this->con = DB::connection('mysql_migration');

        // $this->handleUsers();
        // $this->handleCases();
        // $this->handleBans();
        // $this->handleCategoriesGames();
        // $this->handleTags();
        // $this->handleInstructionsTemplates();

        // $this->handleMods();

        // $this->modIds = Arr::keyBy(Mod::select('id')->get()->toArray(), 'id');
        // $this->handleFollowsAndSubs();

        // $this->handleComments();
        // $this->handleForums();
        // $this->handleLikes();
        // $this->handlePopularityLog();
        // $this->handleModDownloads();
        // $this->handleModViews(); 

        // $this->handleUpdateMods();
        $this->handleUpdateFiles();
        return Command::SUCCESS;
    }

    public function handleUsers()
    {
        $bar = $this->progress('Converting users', $this->con->table('users')->count());

        $this->con->table('users')
            ->join('mws_user_prefs', 'users.uid', '=', 'mws_user_prefs.uid')
            ->join('userfields', 'users.uid', '=', 'userfields.ufid')
        ->orderBy('users.uid')->chunk(1000, function($users) use ($bar) {
            foreach ($users as $user) {
                $bar->advance();

                if ($user->uid == 1) {
                    continue; // ModWorkshop account can be ignored.
                }

                User::forceCreate([
                    'id' => $user->uid,
                    'name' => html_entity_decode($user->username),
                    'avatar' => $user->avatar, // TODO: do we want to store user images in images table, therefore making this column legacy?
                    'custom_color' => Str::limit($user->customcolor, 6, ''),
                    // 'unique_name' => Utils::getUniqueName($user->username),
                    'custom_title' => $user->usertitle, # Changed
                    'created_at' => $this->handleUnixDate($user->regdate), # Changed
                    'last_online' => $this->handleUnixDate($user->lastvisit), # Changed
                    'invisible' => $user->invisible == 1,
                    'banner' => $user->banner,
                    'private_profile' => $user->private_profile,
                    'bio' =>  $user->fid2 ?? '' # Lol
                ]);

                SocialLogin::create([
                    'social_id' => 'steam',
                    'special_id' => $user->loginname,
                    'user_id' => $user->uid,
                    'created_at' => $this->handleUnixDate($user->regdate)
                ]);
            }
        });

        $bar->finish();

        $this->resetAutoIncrement('users');
    }

    public function handleCases()
    {
        $bar = $this->progress('Converting cases', $this->con->table('mws_cases')->count());
        $cases = $this->con->table('mws_cases')->get();
        $STRIKES_TO_WARNING_DURATIONS = [
            1 => Carbon::now()->addMonths(3),
            2 => Carbon::now()->addMonths(6),
            3 => Carbon::now()->addYear(),
        ];

        foreach ($cases as $case) {
            $bar->advance();
            if ($case->strikes) {
                $strikes = max(1, min($case->strikes, 3));
                for ($i=0; $i < STRIKES_TO_WARNINGS[$strikes]; $i++) { 
                    if (User::where('id', $case->uid)->exists()) {
                        UserCase::forceCreate([
                            'user_id' => $case->uid, # Changed
                            'mod_user_id' => $case->moduid, # Changed
                            'reason' => $case->notes,
                            'created_at' => $this->handleUnixDate($case->date),
                            'updated_at' => $this->handleUnixDate($case->date),
                            'expire_date' => $STRIKES_TO_WARNING_DURATIONS[$strikes],
                            'pardoned' => !$case->active
                        ]);
                    }
                }
            }
        }

        $bar->finish();
    }

    public function handleBans()
    {
        $bar = $this->progress('Converting bans', $this->con->table('banned')->count());
        $bans = $this->con->table('banned')->get();

        foreach ($bans as $ban) {
            $bar->advance();
            if (!User::where('id', $ban->uid)->exists()) {
                continue;
            }

            $admin = $ban->admin;

            if (!User::where('id', $admin)->exists()) {
                $admin = 1;
            }

            Ban::forceCreate([
                'user_id' => $ban->uid,
                'reason' => $ban->reason,
                'mod_user_id' => $admin, # Changed
                'created_at' => $this->handleUnixDate($ban->dateline),
                'updated_at' => $this->handleUnixDate($ban->dateline),
            ]);
        }

        $bar->finish();
    }

    public function handleCategoriesGames()
    {
        $bar = $this->progress('Converting games and categories', $this->con->table('mydownloads_categories')->count());
        $cats = $this->con->table('mydownloads_categories')->get();
        foreach ($cats as $cat) {
            if (!$cat->parent) { # Game
                Game::forceCreate([
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
                Category::forceCreate([
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
            Tag::forceCreate([
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
                Tag::forceCreate([
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
        $bar = $this->progress('Converting mods', $this->con->table('mods_instructions_templates')->count());
        $templates = $this->con->table('mods_instructions_templates')->get();
        foreach ($templates as $template) {
            $bar->advance();
            $games = explode(',', $template->categories);
            InstructsTemplate::forceCreate([
                'id' => $template->instid, # Changed
                'name' => $template->name,
                'instructions' => $template->instructions,
                'localized' => $template->localized,
                'game_id' => $games[0], # Make sure only one game is defined per v3's standards / Changed
            ]);

            $deps = json_decode($template->depends_on);
            foreach ($deps as $dep) {
                Dependency::forceCreate([
                    'name' => $dep->id ?? '',
                    'url' => $dep->url ?? null,
                    'mod_id' => is_integer($dep->id) ? $dep->id : null,
                    'offsite' => !!$dep->id,
                    'optional' => $dep->optional,
                    'dependable_type' => 'instructs_template',
                    'dependable_id' => $template->instid,
                ]);
            }
        }

        $bar->finish();
        $this->resetAutoIncrement('instructs_templates');
    }
    public function handleUpdateMods()
    {
        $bar = $this->progress('Fixing mods', $this->con->table('mydownloads_downloads')->count());
        $this->con->table('mydownloads_downloads')->orderBy('did')->chunk(10000, function($mods) use ($bar) {
            foreach ($mods as $mod) {
                $bar->advance();

                /** @var Mod */
                $newMod = Mod::where('id', $mod->did)->first();

                // $images = Image::where('mod_id', $mod->did)->get();
                // foreach ($images as $image) {
                //     if ('https://modworkshop.net/mydownloads/previews/'.$image->file == $mod->banner) {
                //         $newMod['banner_id'] = $image->id;
                //         break;
                //     }
                // }

                // $file = File::where('mod_id', $mod->did)->first();
                // $link = Link::where('mod_id', $mod->did)->first();
                // if (!isset($link) && !empty($mod->url)) {
                //     $link = Link::forceCreate([
                //         'user_id' => $newMod->user_id,
                //         'mod_id' => $newMod->id,
                //         'url' => $mod->url,
                //     ]);
                // }

                $newMod->published_at = $this->handleUnixDate($mod->pub_date);
                $newMod->created_at = $this->handleUnixDate($mod->pub_date ?? $mod->date);
                $newMod->bumped_at = $this->handleUnixDate($mod->date);
                $newMod->updated_at = $this->handleUnixDate($mod->date);

                if ($newMod->visibility == Visibility::public) {
                    $newMod->published_at = $newMod->bumped_at;
                }

                // $newMod->thumbnail_id = !empty($mod->thumbnail_id) ? $mod->thumbnail_id : null;
                // $newMod->legacy_banner_url = isset($newMod->banner_id) ? null : $mod->banner;
                // $newMod->download_type = isset($link) ? 'link' : 'file';
                // $newMod->download_id = $link?->id ?? $file?->id;

                /** @var Mod */
                $newMod->calculateFileStatus();
            }
        });

        $bar->finish();
        $this->resetAutoIncrement('mods');
        $this->resetAutoIncrement('files');
        $this->resetAutoIncrement('images');
    }

    public function handleUpdateFiles()
    {
        $bar = $this->progress('Fixing files', $this->con->table('mydownloads_files')->count());
        $this->con->table('mydownloads_files')->orderBy('did')->chunk(10000, function($files) use ($bar) {
            foreach ($files as $file) {
                $bar->advance();
                $file = File::where('id', $file->fid)->first();
                if (isset($file)) {
                    $file->update([
                        'user_id' => $file->uid ?? $file->mod->user_id,
                    ]);
                }

            }
        });
        $bar->finish();
    }

    public function handleMods()
    {
        $bar = $this->progress('Converting mods', $this->con->table('mydownloads_downloads')->count());
        $this->con->table('mydownloads_downloads')->orderBy('did')->chunk(10000, function($mods) use ($bar) {
            foreach ($mods as $mod) {
                $bar->advance();
                # Score is recalculated.
                # Invited removed due to underuse.
                
                $date = $this->handleUnixDate($mod->date);
                $publishDate = null;
                if (empty($mod->pub_date) && (NEW_VISIBILITY[$mod->hidden] ?? Visibility::public) == Visibility::public) {
                    $publishDate = $date;
                } else {
                    $publishDate = $this->handleUnixDate($mod->pub_date);
                }

                $newMod = Mod::forceCreate([
                    'id' => $mod->did,
                    'category_id' => Category::where('id', $mod->cid)->exists() ? $mod->cid : null, # Changed
                    'game_id' => $mod->root, # Changed
                    'name' => Str::limit($mod->name, 150),
                    'desc' => $mod->description, # Changed
                    'instructions' => $mod->instructions ?? '',
                    'short_desc' => Str::limit($mod->short_description, 150), # Changed
                    'changelog' => $mod->changelog,
                    'visibility' => NEW_VISIBILITY[$mod->hidden], # Changed
                    'instructs_template_id' => !empty($mod->instid) ? $mod->instid : null, # Changed
                    'downloads' => $mod->downloads,
                    'likes' => $mod->likes,
                    'views' => $mod->views,
                    'user_id' => $mod->submitter_uid, # Changed
                    'license' => $mod->license,
                    'version' => $mod->version,
                    'bumped_at' => $date, # Changed
                    'created_at' => $this->handleUnixDate($mod->pub_date ?? $mod->date), # New #Handle dates
                    'updated_at' => $date, # New
                    'published_at' => $publishDate, # Changed
                    'donation' => $mod->receiver_email, # Changed
                    'suspended' => $mod->suspended_status == 1, # Changed into a boolean
                    'comments_disabled' => $mod->comments_disabled == 1, # Changed into a boolean
                    'has_download' => false, # Manually recalculate using v3 conditions :)
                    'approved' => $mod->file_status != 2,
                ]);

                $deps = json_decode($mod->depends_on);
                if (isset($deps)) {
                    foreach ($deps as $dep) {
                        Dependency::forceCreate([
                            'name' => $dep->id ?? '',
                            'url' => $dep->url ?? null,
                            'mod_id' => is_integer($dep->id) ? $dep->id : null,
                            'offsite' => !!$dep->id,
                            'optional' => $dep->optional,
                            'dependable_type' => 'mod',
                            'dependable_id' => $newMod->id,
                        ]);
                    }
                }

                $images = $this->con->table('mws_images')->where('did', $newMod->id)->get();
                $foundBanner = null;
                foreach ($images as $image) {
                    Image::forceCreate([
                        'id' => $image->id,
                        'mod_id' => $image->did,
                        'user_id' => $image->uid || $newMod->user_id,
                        'file' => $image->file,
                        'type' => $image->filetype,
                        'has_thumb' => $image->has_thumb == 1,
                        'size' => $image->filesize,
                        'created_at' => $this->handleUnixDate($image->date),
                        'updated_at' => $this->handleUnixDate($image->date),
                    ]);

                    if ('https://modworkshop.net/mydownloads/previews/'.$image->file == $mod->banner) {
                        $foundBanner = $image->id;
                    }
                }

                $files = $this->con->table('mydownloads_files')->where('did', $newMod->id)->get();
                $firstModFileId = null;
                foreach ($files as $file) {
                    $newFile = File::forceCreate([
                        'id' => $file->fid,
                        'name' => $file->name,
                        'mod_id' => $file->did,
                        'user_id' => $file->uid ?? $newMod->user_id,
                        'file' => $file->file,
                        'desc' => $file->description,
                        // 'image_id' => $file->image, #TODO: Deal with this, has also thumbnail column
                        'type' => $file->filetype,
                        'size' => $file->filesize,
                        'created_at' => $this->handleUnixDate($file->date),
                        'updated_at' => $this->handleUnixDate($file->date),
                    ]);
                    $firstModFileId ??= $newFile->id;
                }

                $link = null;
                if (!empty($mod->url)) {
                    $link = Link::forceCreate([
                        'user_id' => $newMod->user_id,
                        'mod_id' => $newMod->id,
                        'url' => $mod->url,
                    ]);
                }

                $newMod->thumbnail_id = !empty($mod->thumbnail_id) ? $mod->thumbnail_id : null;
                $newMod->legacy_banner_url = $foundBanner ? null : $mod->banner;
                $newMod->download_type = isset($mod->url) ? 'link' : 'file';
                $newMod->download_id = !empty($mod->url) ? $link->id : $firstModFileId;

                if (isset($foundBanner)) {
                    $newMod->banner_id = $foundBanner;
                }

                $newMod->calculateFileStatus();

                foreach (explode(',', $mod->tags) as $id) {
                    if (!empty($id) && Tag::whereId($id)->exists()) {
                        Taggable::forceCreate([
                            'tag_id' => intval($id),
                            'taggable_type' => 'mod',
                            'taggable_id' => $newMod->id,
                        ]);
                    }
                }
    
                foreach (explode(',', $mod->collaborators) as $id) {
                    if (is_numeric($id) && User::where('id', $id)->exists()) {
                        ModMember::forceCreate([
                            'mod_id' => $newMod->id,
                            'user_id' => $id,
                            'level' => 'collaborator',
                            'accepted' => true
                        ]);
                    }
                }
    
                foreach (explode(',', $mod->invited) as $id) {
                    if (is_int($id) && User::where('id', $id)->exists()) {
                        ModMember::forceCreate([
                            'mod_id' => $newMod->id,
                            'user_id' => $id,
                            'level' => 'viewer',
                            'accepted' => true
                        ]);
                    }
                }
            }
        });

        $bar->finish();
        $this->resetAutoIncrement('mods');
        $this->resetAutoIncrement('files');
        $this->resetAutoIncrement('images');
    }

    public function handleFollowsAndSubs()
    {
        $bar = $this->progress('Converting discussion subscriptions', $this->con->table('mws_subs')->count());
        $this->con->table('mws_subs')->orderBy('sid')->chunk(10000, function($subs) use ($bar) {
            $insert = [];

            foreach ($subs as $sub) {
                $type = NEW_SUB_TYPES[$sub->type];
                $bar->advance();

                if (
                    $type == 'mod' && !isset($this->modIds[$sub->id]) 
                    || $type == 'comment' && !Comment::where('id', $sub->id)->exists() 
                    || !User::where('id', $sub->uid)->exists()
                ) {
                    continue;
                }
    
                $insert[] = [
                    'user_id' => $sub->uid,
                    'subscribable_type' => $type,
                    'subscribable_id' => $sub->id
                ];
            }

            Subscription::insert($insert);
        });
        $bar->finish();

        $bar = $this->progress('Converting followed mods', $this->con->table('mws_following_mods')->count());
        $this->con->table('mws_following_mods')->orderBy('fid')->chunk(1000, function($follows) use ($bar) {
            $insert = [];
            foreach ($follows as $follow) {
                $bar->advance();
                if (Mod::where('id', $follow->follow)->exists() && User::where('id', $follow->uid)->exists()) { // Make sure the mod exists
                    $insert[] = [
                        'notify' => true,
                        'user_id' => $follow->uid,
                        'mod_id' => $follow->follow,
                    ];
                }
                FollowedMod::insert($insert);
            }
        });
        $bar->finish();

        $bar = $this->progress('Converting followed games', $this->con->table('mws_following_cats')->count());
        $this->con->table('mws_following_cats')->orderBy('fid')->chunk(1000, function($follows) use ($bar) {
            foreach ($follows as $follow) {
                $bar->advance();
                if (Game::where('id', $follow->follow)->exists() && User::where('id', $follow->uid)->exists()) { // Following categories is no more
                    FollowedGame::create([
                        'user_id' => $follow->uid,
                        'game_id' => $follow->follow,
                    ]);
                }
            }
        });
        $bar->finish();

        $bar = $this->progress('Converting followed users', $this->con->table('mws_following_users')->count());
        $this->con->table('mws_following_users')->orderBy('fid')->chunk(1000, function($follows) use ($bar) {
            foreach ($follows as $follow) {
                $bar->advance();
                if (User::where('id', $follow->follow)->exists() && User::where('id', $follow->uid)->exists()) { // Make sure the user exists
                    FollowedUser::create([
                        'notify' => false,
                        'user_id' => $follow->uid,
                        'follow_user_id' => $follow->follow,
                    ]);
                }
            }
        });
        $bar->finish();
    }

    public function handleComments()
    {
        $bar = $this->progress('Converting mod comments', $this->con->table('mydownloads_comments')->count());
        $this->con->table('mydownloads_comments')->orderBy('uid')->where('uid', '!=', 0)->chunk(1000, function($comments) use ($bar) {
            foreach ($comments as $comment) {
                # Removed cid (unused)
                $bar->advance();

                if (!empty($comment->replyid) && !Comment::where('id', $comment->replyid)->exists() ) {
                    continue;
                }

                if (!User::where('id', $comment->uid)->exists()) {
                    continue;
                }

                Comment::forceCreate([
                    'user_id' => $comment->uid, # Changed,
                    'commentable_type' => 'mod',
                    'commentable_id' => $comment->did, # Changed
                    'content' => $comment->comment, # Changed
                    'updated_at' => $this->handleUnixDate($comment->date_edited), #Changed, handle date
                    'created_at' => $this->handleUnixDate($comment->date), # Changed
                    'pinned' => $comment->pinned == '1',
                    'reply_to' => !empty($comment->replyid) ? $comment->replyid : null
                ]);
            }
        });

        $bar->finish();
    }

    public function handleForums()
    {
        $bar = $this->progress('Converting forums', $this->con->table('forums')->count());
        $forums = $this->con->table('forums')->where('type', 'f')->get();
        foreach ($forums as $forum) {
            $bar->advance();
            ForumCategory::forceCreate([
                'forum_id' => 1,
                'id' => $forum->fid,
                'name' => $forum->name,
                'desc' => $forum->description,
            ]);
        }
        $bar->finish();


        $bar = $this->progress('Converting threads', $this->con->table('threads')->count());
        $threads = $this->con->table('threads')->get();
        foreach ($threads as $thread) {
            $bar->advance();
            $firstPost = $this->con->table('posts')->where('pid', $thread->firstpost)->first();
            if (isset($firstPost) && $thread->uid) {
                $insert = [
                    'forum_id' => 1,
                    'id' => $thread->tid,
                    'name' => $thread->subject,
                    'content' => $firstPost->message,
                    'category_id' => $thread->fid,
                    'user_id' => $thread->uid,
                    'last_user_id' => !empty($thread->lastposteruid) ? $thread->lastposteruid : $thread->uid,
                    'updated_at' => $this->handleUnixDate($thread->dateline),
                    'created_at' => $this->handleUnixDate($thread->dateline),
                    'bumped_at' => $this->handleUnixDate($thread->lastpost),
                    'pinned_at' => $thread->sticky ? Carbon::now() : null,
                    'locked' => $thread->closed == 1,
                    'locked_by_mod' => $thread->closed == 1,
                ];
                Thread::forceCreate($insert);
            }
        }
        $bar->finish();

        $bar = $this->progress('Converting thread subscriptions', $this->con->table('threadsubscriptions')->count());
        $subs = $this->con->table('threadsubscriptions')->get();
        foreach ($subs as $sub) {
            $bar->advance();
            Subscription::forceCreate([
                'user_id' => $sub->uid,
                'subscribable_type' => 'thread',
                'subscribable_id' => $sub->tid
            ]);
        }
        $bar->finish();


        $bar = $this->progress('Converting thread replies', $this->con->table('posts')->count());
        $this->con->table('posts')->orderBy('pid')->where('uid', '!=', 0)->chunk(1000, function($posts) use ($bar) {
            foreach ($posts as $post) {
                # Removed cid (unused)
                $bar->advance();

                if (empty($post->replyto)) {
                    continue;
                }

                if (!User::where('id', $post->uid)->exists()) {
                    continue;
                }

                Comment::forceCreate([
                    'user_id' => $post->uid,
                    'commentable_type' => 'thread',
                    'commentable_id' => $post->tid,
                    'content' => $post->message,
                    'updated_at' => $this->handleUnixDate($post->dateline),
                    'created_at' => $this->handleUnixDate($post->dateline),
                ]);
            }
        });
        $bar->finish();
    }

    public function handleModDownloads()
    {
        $bar = $this->progress('Converting mod downloads', $this->con->table('mods_downloads')->count());

        $this->con->table('mods_downloads')->orderBy('uid')->chunk(10000, function($downloads) use ($bar) {
            $insertAtOnce = [];
            $insertAtOnceIpless = [];
            $insertAtOnceUserless = [];
            $insertBroken = [];
            foreach ($downloads as $download) {
                $bar->advance();

                if (!isset($this->modIds[$download->did])) {
                    continue;
                }

                $date = $this->handleUnixDate($download->date);
                $insert = [
                    'mod_id' => $download->did, # Changed
                    'updated_at' => $date, # Changed
                    'created_at' => $date
                ];
                
                $hasUser = false;
                $hasIp = false;
                if ($download->uid) {
                    $insert['user_id'] = $download->uid;
                    $hasUser = true;
                }

                if (!empty($download->ipaddress) && !is_numeric($download->ipaddress)) {
                    $insert['ip_address'] = $download->ipaddress;
                    $hasIp = true;
                }

                if ($hasUser && $hasIp) {
                    $insertAtOnce[] = $insert;
                } else if ($hasIp) {
                    $insertAtOnceUserless[] = $insert;
                } else if ($hasUser) {
                    $insertAtOnceIpless[] = $insert;
                } else {
                    $insertBroken[] = $insert;
                }
            }

            ModDownload::insert($insertAtOnceIpless);
            ModDownload::insert($insertAtOnceIpless);
            ModDownload::insert($insertAtOnceUserless);
            ModDownload::insert($insertBroken);
            unset($insertAtOnce);
            unset($insertAtOnceIpless);
            unset($insertAtOnceUserless);
            unset($insertBroken);
        });

        $bar->finish();
    }

    public function handleModViews()
    {
        $bar = $this->progress('Converting mod views', $this->con->table('mydownloads_views')->count());
        $this->con->table('mydownloads_views')->orderBy('uid')->chunk(10000, function($views) use ($bar) {
            $insertAtOnce = [];
            $insertAtOnceIpless = [];
            $insertAtOnceUserless = [];
            $insertBroken = [];

            foreach ($views as $view) {
                $bar->advance();

                if (!isset($this->modIds[$view->did])) {
                    continue;
                }

                $insert = [
                    'mod_id' => $view->did, # Changed
                ];

                $hasUser = false;
                $hasIp = false;
                if ($view->uid) {
                    $insert['user_id'] = $view->uid;
                    $hasUser = true;
                }

                if (!empty($view->ipaddress) && !is_numeric($view->ipaddress)) {
                    $insert['ip_address'] = $view->ipaddress;
                    $hasIp = true;
                }

                if ($hasUser && $hasIp) {
                    $insertAtOnce[] = $insert;
                } else if ($hasIp) {
                    $insertAtOnceUserless[] = $insert;
                } else if ($hasUser) {
                    $insertAtOnceIpless[] = $insert;
                } else {
                    $insertBroken[] = $insert;
                }
            }

            ModView::insert($insertAtOnceIpless);
            ModView::insert($insertAtOnceIpless);
            ModView::insert($insertAtOnceUserless);
            ModView::insert($insertBroken);
            unset($insertAtOnce);
            unset($insertAtOnceIpless);
            unset($insertAtOnceUserless);
            unset($insertBroken);
        });

        $bar->finish();
    }

    public function handleLikes()
    {
        $bar = $this->progress('Converting mod likes', $this->con->table('mydownloads_ratings')->count());
        $this->con->table('mydownloads_ratings')->orderBy('uid')->chunk(1000, function($likes) use ($bar) {
            $insert = [];
            foreach ($likes as $like) {
                # Removed IP (redundant, only users can like mods!)
                # Altho it does raise an interesting question about bots and bot-liking mods ^
                # Removed type (redundant, from the days we had dislikes)
                $bar->advance();

                // So fucking much invalid data
                if (!isset($this->modIds[$like->did])) {
                    continue;
                }

                if (!User::where('id', $like->uid)->exists()) {
                    continue;
                }

                $insert[] = [
                    'mod_id' => $like->did,
                    'user_id' => $like->uid,
                    'updated_at' => $this->handleUnixDate($like->stamp), # Handle date
                    'created_at' => $this->handleUnixDate($like->stamp)
                ];
            }

            ModLike::insert($insert);
        });
        
        $bar->finish();
    }

    public function handlePopularityLog()
    {
        $bar = $this->progress('Converting popularity logs', $this->con->table('mods_monthly_logs')->count());
        $this->con->table('mods_monthly_logs')->orderBy('uid')->where('')->chunk(10000, function($logs) use ($bar) {
            $insert = [];
            $insertUserless = [];
            foreach ($logs as $log) {
                $bar->advance();

                if (!isset($this->modIds[$log->did]) || $log->type == 2) {
                    continue;
                }

                $newLog = [
                    'ip_address' => $log->ipaddress,
                    'mod_id' => $log->did,
                    'updated_at' => $this->handleUnixDate($log->date),
                    'type' => NEW_LOG_TYPES[$log->type] # Really changed
                ];

                if (!empty($log->uid)) {
                    $newLog['user_id'] = $log->uid;
                    $insert[] = $newLog;
                } else {
                    $insertUserless[] = $newLog;
                }
            }

            PopularityLog::insert($insert);
            PopularityLog::insert($insertUserless);
        });

        $bar->finish();
    }
}
