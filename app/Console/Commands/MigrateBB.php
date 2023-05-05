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
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\SocialLogin;
use App\Models\Tag;
use App\Models\Taggable;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Supporter;
use App\Models\Thread;
use App\Models\UserCase;
use App\Models\Visibility;
use App\Services\Utils;
use Arr;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Application;
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

const STAFF_ONLY_FORUMS = [
    35 => true,
    85 => true,
    26 => true
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
    private array $userIds = [];
    private array $roleIds = [];
    private array $tagIds = [];

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

        $t = time();
        $this->info("Converting old-ass data to new-and-improved data!");
        ini_set("memory_limit", -1);
        set_time_limit(1000000000); // This can take A looong time.

        $this->con = DB::connection('mysql_migration');

        $this->handleRoles();
        $this->handleUsers();

        $this->handleSupporters();

        $this->userIds = Arr::keyBy(User::select('id')->get()->toArray(), 'id');

        $this->handleCases();
        $this->handleBans();
        $this->handleCategoriesGames();
        $this->handleTags();
        $this->handleInstructionsTemplates();

        $this->handleMods();
        $this->handleFileThumbnails();

        $this->modIds = Arr::keyBy(Mod::select('id')->get()->toArray(), 'id');
        $this->handleFollowsAndSubs();

        $this->handleComments();
        $this->handleForums();
        $this->handleLikes();
        $this->handlePopularityLog();
        // $this->handleModDownloads();
        // $this->handleModViews(); 

        // $this->handleUpdateMods();
        // $this->handleUpdateFiles();

        $this->info('This took '.((time()-$t)/60).' minutes');

        return Command::SUCCESS;
    }

    public function handleUsers()
    {
        $bar = $this->progress('Converting users', $this->con->table('users')->count());

        $this->con->table('users')
            ->join('mws_user_prefs', 'users.uid', '=', 'mws_user_prefs.uid')
            ->join('userfields', 'users.uid', '=', 'userfields.ufid')
        ->chunkById(1000, function($users) use ($bar) {
            $insertUsers = [];
            $insertLogins = [];
            $insertUserRoles = [];
            
            foreach ($users as $user) {
                $bar->advance();

                if ($user->uid == 1) {
                    continue; // ModWorkshop account can be ignored.
                }

                $insertUsers[] = [
                    'id' => $user->uid,
                    'name' => html_entity_decode($user->username, encoding: 'UTF-8'),
                    'avatar' => preg_replace(["/\/uploads\/avatars\//", '/\?dateline=\w+/'], '', $user->avatar),
                    'custom_color' => Str::limit($user->customcolor, 6, ''),
                    'unique_name' => Utils::getUniqueNameCached($user->username),
                    'custom_title' => $user->usertitle, # Changed
                    'created_at' => $this->handleUnixDate($user->regdate), # Changed
                    'last_online' => $this->handleUnixDate($user->lastvisit), # Changed
                    'invisible' => $user->invisible == 1,
                    'banner' => $user->banner,
                    'private_profile' => $user->private_profile,
                    'activated' => true,
                    'bio' =>  $user->fid2 ?? '' # Lol
                ];

                $insertLogins[] = [
                    'social_id' => 'steam',
                    'special_id' => $user->loginname,
                    'user_id' => $user->uid,
                    'created_at' => $this->handleUnixDate($user->regdate)
                ];

                $useRoles = [$user->usergroup, $user->displaygroup, ...explode(',', $user->additionalgroups)];
                $addedAlready = [];
                foreach ($useRoles as $roleId) {
                    $newRoleId = $this->roleIds[intval($roleId)] ?? null;
                    if (isset($newRoleId) && !isset($addedAlready[$newRoleId])) {
                        $addedAlready[$newRoleId] = true;
                        $insertUserRoles[] = [
                            'user_id' => $user->uid,
                            'role_id' => $newRoleId
                        ];
                    }
                }
            }

            User::insert($insertUsers);
            SocialLogin::insert($insertLogins);
            RoleUser::insert($insertUserRoles);

            unset($insertUsers);
            unset($insertLogins);
        }, 'users.uid', 'uid');

        $bar->finish();

        $this->resetAutoIncrement('users');
    }

    public function handleRoles()
    {
        $bar = $this->progress('Converting roles', $this->con->table('usergroups')->count());
        $roles = $this->con->table('usergroups')->get();

        $IGNORE_IDS = [
            1 => true,
            2 => true,
            5 => true,
            7 => true,
            30 => true,
            17 => true,
            41 => true,
            29 => true,
            6 => true,
            3 => true,
            34 => true,
            33 => true
        ];

        $MOD_IDS = [
            4 => true,
            18 => true
        ];

        foreach ($roles as $role) {
            if (!($IGNORE_IDS[$role->gid] ?? false)) {
                $newRole = Role::forceCreate([
                    'name' => $role->title,
                    'desc' => $role->description,
                    'is_vanity' => ($MOD_IDS[$role->gid] ?? false) == false,
                    'order' => 1
                ]);

                $this->roleIds[$role->gid] = $newRole->id;
            }

            $bar->advance();
        }
    }

    public function handleSupporters()
    {
        $bar = $this->progress('Converting supporters', $this->con->table('mysubscriptions_log')->count());
        $subs = $this->con->table('mysubscriptions_log')->get();

        foreach($subs as $sub) {
            Supporter::forceCreate([
                'user_id' => $sub->uid,
                'created_at' => $this->handleUnixDate($sub->timestamp),
                'expire_date' => $this->handleUnixDate($sub->enddate),
                'expired' => $sub->expired
            ]);
        }
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
                    if (isset($this->userIds[$case->uid])) {
                        UserCase::forceCreate([
                            'user_id' => $case->uid, # Changed
                            'mod_user_id' => $case->moduid, # Changed
                            'reason' => $case->notes,
                            'created_at' =>$case->date,
                            'updated_at' => $case->date,
                            'expire_date' => $STRIKES_TO_WARNING_DURATIONS[$strikes],
                            'active' => $case->active
                        ]);
                    }
                }
            }
        }

        unset($cases);
        $bar->finish();
    }

    public function handleBans()
    {
        $bar = $this->progress('Converting bans', $this->con->table('banned')->count());
        $bans = $this->con->table('banned')->get();

        foreach ($bans as $ban) {
            $bar->advance();
            if (!isset($this->userIds[$ban->uid])) {
                continue;
            }

            $admin = $ban->admin;

            if (!isset($this->userIds[$admin])) {
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

        unset($bans);

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
                    'mod_count' => $cat->downloads
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

        unset($cats);
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
                $newTag = Tag::forceCreate([
                    'name' => $tag->tag,
                    'color' => $tag->color,
                    'notice' => $tag->notice,
                    'notice_localized' => $tag->notice_localized == 1,
                    'notice_type' => NEW_TAG_TYPES[$tag->notice_type],
                    'type' => 'mod',
                    'game_id' => intval($games[$i])
                ]);

                $this->tagIds[$tag->tid][intval($games[$i])] = $newTag->id;
            }
        }

        unset($multipleGameTags);
        $bar->finish();
    }

    public function handleInstructionsTemplates()
    {
        $bar = $this->progress('Converting instructions templates', $this->con->table('mods_instructions_templates')->count());
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

        unset($templates);

        $bar->finish();
        $this->resetAutoIncrement('instructs_templates');
    }
    public function handleUpdateMods()
    {
        $bar = $this->progress('Fixing mods', $this->con->table('mydownloads_downloads')->count());
        $mods = $this->con->table('mydownloads_downloads')->get();
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

        unset($mods);

        $bar->finish();
        $this->resetAutoIncrement('mods');
        $this->resetAutoIncrement('files');
        $this->resetAutoIncrement('images');
    }

    public function handleUpdateFiles()
    {
        $bar = $this->progress('Fixing files', $this->con->table('mydownloads_files')->count());
        $files = $this->con->table('mydownloads_files')->get();

        foreach ($files as $file) {
            $bar->advance();
            $file = File::where('id', $file->fid)->first();
            if (isset($file)) {
                $file->update([
                    'user_id' => $file->uid ?? $file->mod->user_id,
                ]);
            }
        }

        unset($files);

        $bar->finish();
    }

    public function handleMods()
    {
        $bar = $this->progress('Converting mods', $this->con->table('mydownloads_downloads')->count());
        $mods = $this->con->table('mydownloads_downloads')->get();

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
            $insertDeps = [];
            if (isset($deps)) {
                foreach ($deps as $dep) {
                    $insertDeps[] = [
                        'name' => $dep->id ?? '',
                        'url' => $dep->url ?? null,
                        'mod_id' => is_integer($dep->id) ? $dep->id : null,
                        'offsite' => !!$dep->id,
                        'optional' => $dep->optional,
                        'dependable_type' => 'mod',
                        'dependable_id' => $newMod->id,
                    ];
                }
            }
            Dependency::insert($insertDeps);

            $images = $this->con->table('mws_images')->where('did', $newMod->id)->get();
            $foundBanner = null;
            $insertImages = [];
            foreach ($images as $image) {
                $date = $this->handleUnixDate($image->date);
                $insertImages[] = [
                    'id' => $image->id,
                    'mod_id' => $image->did,
                    'user_id' => $image->uid > 0 ? $image->uid : $newMod->user_id,
                    'file' => $image->file,
                    'type' => $image->filetype,
                    'has_thumb' => $image->has_thumb == 1,
                    'size' => $image->filesize,
                    'created_at' => $date,
                    'updated_at' => $date,
                ];

                if ('https://modworkshop.net/mydownloads/previews/'.$image->file == $mod->banner) {
                    $foundBanner = $image->id;
                }
            }
            Image::insert($insertImages);

            $files = $this->con->table('mydownloads_files')->where('did', $newMod->id)->get();
            $firstModFileId = null;
            $insertFiles = [];
            foreach ($files as $file) {
                $date = $this->handleUnixDate($file->date);
                $insertFiles[] = [
                    'id' => $file->fid,
                    'name' => $file->name,
                    'mod_id' => $file->did,
                    'user_id' => $file->uid ?? $newMod->user_id,
                    'file' => $file->file,
                    'desc' => $file->description,
                    'type' => $file->filetype,
                    'size' => $file->filesize,
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
                $firstModFileId ??= $file->fid;
            }
            File::insert($insertFiles);

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
            $newMod->download_type = !empty($mod->url) ? 'link' : 'file';
            $newMod->download_id = !empty($mod->url) ? $link->id : $firstModFileId;

            if (isset($foundBanner)) {
                $newMod->banner_id = $foundBanner;
            }

            $newMod->calculateFileStatus();
            $insertTags = [];
            foreach (explode(',', $mod->tags) as $id) {
                if (isset($this->tagIds[$id]) && isset($this->tagIds[$id][$newMod->game_id])) {
                    $id = $this->tagIds[$id][$newMod->game_id];
                }

                if (!empty($id) && Tag::whereId($id)->exists()) {
                    $insertTags[] = [
                        'tag_id' => intval($id),
                        'taggable_type' => 'mod',
                        'taggable_id' => $newMod->id,
                    ];
                }
            }
            Taggable::insert($insertTags);

            $insertMembers = [];
            foreach (explode(',', $mod->collaborators) as $id) {
                if (is_numeric($id) && isset($this->userIds[$id])) {
                    $insertMembers[] = [
                        'mod_id' => $newMod->id,
                        'user_id' => $id,
                        'level' => 'collaborator',
                        'accepted' => true
                    ];
                }
            }

            foreach (explode(',', $mod->invited) as $id) {
                if (is_int($id) && isset($this->userIds[$id])) {
                    $insertMember[] = [
                        'mod_id' => $newMod->id,
                        'user_id' => $id,
                        'level' => 'viewer',
                        'accepted' => true
                    ];
                }
            }

            ModMember::insert($insertMembers);
        }

        unset($mods);
        unset($insertMembers);
        unset($insertImages);
        unset($insertTags);
        unset($insertFiles);
        unset($insertDeps);

        $bar->finish();
        $this->resetAutoIncrement('mods');
        $this->resetAutoIncrement('files');
        $this->resetAutoIncrement('images');
    }

    public function handleFileThumbnails()
    {
        $bar = $this->progress('Converting file thumbnails', $this->con->table('mydownloads_files')->count());
        $files = $this->con->table('mydownloads_files')->whereNotNull('image')->get();

        foreach ($files as $file) {
            if (!empty($file->image)) {
                $image = Image::forceCreate([
                    'mod_id' => $file->did,
                    'user_id' => $file->uid,
                    'file' => $file->image,
                    'type' => Str::of($file->image)->match('/\w+\.(\w+)/'),
                    'has_thumb' => !empty($file->thumbnail),
                    'size' => 0, //TODO
                    'created_at' => $this->handleUnixDate($file->date),
                    'updated_at' => $this->handleUnixDate($file->date),
                ]);

                File::where('id', $file->fid)->update([
                    'image_id' => $image->id
                ]);
            }

            $bar->advance();
        }

        unset($files);
    }

    public function handleFollowsAndSubs()
    {
        $bar = $this->progress('Converting discussion subscriptions', $this->con->table('mws_subs')->count());
        $this->con->table('mws_subs')->chunkById(100000, function($subs) use ($bar) {
            $insert = [];

            foreach ($subs as $sub) {
                $type = NEW_SUB_TYPES[$sub->type];
                $bar->advance();

                if (
                    $type == 'mod' && !isset($this->modIds[$sub->id]) 
                    || $type == 'comment' && !Comment::where('id', $sub->id)->exists() 
                    || !isset($this->userIds[$sub->uid])
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

            unset($subs);
        }, 'sid');
        $bar->finish();

        $bar = $this->progress('Converting followed mods', $this->con->table('mws_following_mods')->count());
        $this->con->table('mws_following_mods')->chunkById(100000, function($follows) use ($bar) {
            $insert = [];
            foreach ($follows as $follow) {
                $bar->advance();
                if (Mod::where('id', $follow->follow)->exists() && isset($this->userIds[$follow->uid])) { // Make sure the mod exists
                    $insert[] = [
                        'notify' => true,
                        'user_id' => $follow->uid,
                        'mod_id' => $follow->follow,
                    ];
                }
            }

            FollowedMod::insert($insert);
            unset($follows);
        }, 'fid');
        $bar->finish();

        $bar = $this->progress('Converting followed games', $this->con->table('mws_following_cats')->count());
        $this->con->table('mws_following_cats')->chunkById(500, function($follows) use ($bar) {
            foreach ($follows as $follow) {
                $bar->advance();
                if (Game::where('id', $follow->follow)->exists() && isset($this->userIds[$follow->uid])) { // Following categories is no more
                    FollowedGame::create([
                        'user_id' => $follow->uid,
                        'game_id' => $follow->follow,
                    ]);
                }
            }

            unset($follows);
        }, 'fid');
        $bar->finish();

        $bar = $this->progress('Converting followed users', $this->con->table('mws_following_users')->count());
        $this->con->table('mws_following_users')->chunkById(500, function($follows) use ($bar) {
            foreach ($follows as $follow) {
                $bar->advance();
                if (isset($this->userIds[$follow->follow]) && isset($this->userIds[$follow->uid])) { // Make sure the user exists
                    FollowedUser::create([
                        'notify' => false,
                        'user_id' => $follow->uid,
                        'follow_user_id' => $follow->follow,
                    ]);
                }
            }

            unset($follows);
        }, 'fid');
        $bar->finish();
    }

    public function handleComments()
    {
        $bar = $this->progress('Converting mod comments', $this->con->table('mydownloads_comments')->count());
        $comments = $this->con->table('mydownloads_comments')->orderBy('replyid')->get();

        $insert = [];
        $insertReplies = [];

        $inserted = [];

        $doInsert = function() use (&$insert, &$insertReplies) {
            if (!empty($insert)) {
                Comment::insert($insert);
            }

            if (!empty($insertReplies)) {
                Comment::insert($insertReplies);
            }

            $insert = [];
            $insertReplies = [];
        };

        $i = 0;
        foreach ($comments as $comment) {
            $bar->advance();

            if (!empty($comment->replyid) && !($inserted[intval($comment->replyid)] ?? false)) {
                continue;
            }

            if (!isset($this->userIds[$comment->uid])) {
                continue;
            }

            $commentInsert = [
                'id' => $comment->cid,
                'user_id' => $comment->uid, # Changed,
                'commentable_type' => 'mod',
                'commentable_id' => $comment->did, # Changed
                'content' => $comment->comment, # Changed
                'updated_at' => $this->handleUnixDate($comment->date_edited), #Changed, handle date
                'created_at' => $this->handleUnixDate($comment->date), # Changed
                'pinned' => $comment->pinned == '1',
            ];

            $inserted[intval($comment->cid)] = true;

            if (!empty($comment->replyid)) {
                $commentInsert['reply_to'] = $comment->replyid;
                $insertReplies[] = $commentInsert;
            } else {
                $insert[] = $commentInsert;
            }

            if ($i % 500) {
                $doInsert();
            }

            $i++;
        }

        $doInsert();

        unset($insert);
        unset($insertReplies);
        unset($comments);

        $this->resetAutoIncrement('comments');

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
                'is_private' => STAFF_ONLY_FORUMS[intval($forum->fid)] ?? false
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
        $posts = $this->con->table('posts')->where('uid', '!=', 0)->get();
        foreach ($posts as $post) {
            # Removed cid (unused)
            $bar->advance();

            if (empty($post->replyto)) {
                continue;
            }

            if (!isset($this->userIds[$post->uid])) {
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

        unset($posts);
        unset($subs);
        unset($forums);
        $bar->finish();
    }

    public function handleModDownloads()
    {
        $bar = $this->progress('Converting mod downloads', $this->con->table('mods_downloads')->count());
        $downloads = $this->con->table('mods_downloads')->lazyById(100000, 'lid');

        $i = 0;
        $insertAtOnce = [];
        $insertAtOnceIpless = [];
        $insertAtOnceUserless = [];
        $insertBroken = [];

        $doInsert = function() use (&$insertAtOnce, &$insertAtOnceIpless, &$insertAtOnceUserless, &$insertBroken) {
            ModView::insert($insertAtOnce);
            ModView::insert($insertAtOnceIpless);
            ModView::insert($insertAtOnceUserless);
            ModView::insert($insertBroken);
    
            $insertAtOnce = [];
            $insertAtOnceIpless = [];
            $insertAtOnceUserless = [];
            $insertBroken = [];    
        };

        foreach ($downloads as $download) {
            $bar->advance();

            if (!isset($this->modIds[$download->did])) {
                continue;
            }

            $insert = [
                'mod_id' => $download->did, # Changed
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

            if ($i % 500 == 0) {
                $doInsert();
            }

            $i++;
        }

        $insertAtOnce = [];
        $insertAtOnceIpless = [];
        $insertAtOnceUserless = [];
        $insertBroken = [];

        $bar->finish();
    }

    public function handleModViews()
    {
        $bar = $this->progress('Converting mod views', $this->con->table('mydownloads_views')->count());
        $views = $this->con->table('mydownloads_views')->lazyById(100000, 'vid');

        $insertAtOnce = [];
        $insertAtOnceIpless = [];
        $insertAtOnceUserless = [];
        $insertBroken = [];

        $i = 0;

        $doInsert = function() use (&$insertAtOnce, &$insertAtOnceIpless, &$insertAtOnceUserless, &$insertBroken) {
            ModView::insert($insertAtOnce);
            ModView::insert($insertAtOnceIpless);
            ModView::insert($insertAtOnceUserless);
            ModView::insert($insertBroken);
    
            $insertAtOnce = [];
            $insertAtOnceIpless = [];
            $insertAtOnceUserless = [];
            $insertBroken = [];    
        };

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

            if ($i % 500 == 0) {
                $doInsert();
            }

            $i++;
        }

        $doInsert();

        unset($insertAtOnce);
        unset($insertAtOnceIpless);
        unset($insertAtOnceUserless);
        unset($insertBroken);
        unset($views);

        $bar->finish();
    }

    public function handleLikes()
    {
        $bar = $this->progress('Converting mod likes', $this->con->table('mydownloads_ratings')->count());
        $this->con->table('mydownloads_ratings')->chunkById(15000, function($likes) use ($bar) {
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

                if (!isset($this->userIds[$like->uid])) {
                    continue;
                }

                $time = $this->handleUnixDate($like->stamp);
                $insert[] = [
                    'mod_id' => $like->did,
                    'user_id' => $like->uid,
                    'updated_at' => $time, # Handle date
                    'created_at' => $time
                ];
            }

            ModLike::insert($insert);
            unset($insert);
            unset($likes);
        }, 'rid');
        
        $bar->finish();
    }

    public function handlePopularityLog()
    {
        $bar = $this->progress('Converting popularity logs', $this->con->table('mods_monthly_logs')->count());
        $logs = $this->con->table('mods_monthly_logs')->get();

        $insert = [];
        $insertUserless = [];

        $i = 0;

        $doInsert = function() use (&$insert, &$insertUserless) {
            if (!empty($insert)) {
                PopularityLog::insert($insert);
            }

            if (!empty($insertUserless)) {
                PopularityLog::insert($insertUserless);
            }

            $insert = [];
            $insertUserless = [];
        };

        foreach ($logs as $log) {
            $bar->advance();

            if (!isset($this->modIds[$log->did]) || $log->type == 3 || $log->type == 4) {
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
    
            if ($i % 500 == 0) {
                $doInsert();
            }

            $i++;
        }

        $doInsert();
        $bar->finish();
        unset($insert);
        unset($insertUserless);
        unset($logs);
    }
    
}
