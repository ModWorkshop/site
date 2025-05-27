<?php

namespace App\Console\Commands;

use App\Models\User;
use DB;
use Illuminate\Console\Command;
use Illuminate\Database\Connection;

class RestoreUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mws:restore-user {user-id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attempts to restore a user using 2 databases. One backup and one empty to export from.';

    private int $userId;

    public function restoreTable(string $tableName, callable $clbk=null, string $key = 'user_id', string $value=null) {
        $value ??= $this->userId;

        $this->info("Restoring table {$tableName} with key {$key} and value {$value}");

        $rows = DB::table($tableName)->where($key, $value)->get();
        $con = DB::connection('sec_pgsql');

        foreach ($rows as $row) {
            if (!$con->table($tableName)->where('id', $row->id)->exists()) {
                $con->table($tableName)->insert((array)$row);
            }
            if (isset($clbk)) {
                $clbk($row, $con);
            }
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user-id'); //71794;
        $this->userId = $userId;

        $con = DB::connection('sec_pgsql');

        $user = DB::table('users')->find($userId);

        if (!isset($user)) {
            $this->error('User not found!');
            return;
        }

        $this->info("Restoring user...");

        $userExtra = DB::table('user_extras')->find($userId);
        $con->table('users')->insert((array)$user);
        if (isset($userExtra)) {
            $con->table('users')->insert((array)$userExtra);
        }

        // Restore user mods
        $this->restoreTable('mods', function(object $obj, Connection $con) {
            // Restore mod images
            $this->restoreTable('images', key: 'mod_id', value: $obj->id);

            // Restore mod files
            $this->restoreTable('files', key: 'mod_id', value: $obj->id);

            // Restore mod links
            $this->restoreTable('links', key: 'mod_id', value: $obj->id);

            // Restore mod suspensions
            $this->restoreTable('suspensions', key: 'mod_id', value: $obj->id);

            // Restore mod tags
            $taggables = DB::table('taggables')->where('taggable_type', 'mod')->where('taggable_id', $obj->id)->get();
            foreach ($taggables as $tag) {
                $con->table('taggables')->insert((array)$tag);
            }

            // Restore mod comments
            $comments = DB::table('comments')->where('commentable_type', 'mod')->where('commentable_id', $obj->id)->get();
            foreach ($comments as $comment) {
                $con->table('comments')->insert((array)$comment);
            }
        });

        // Restore user threads
        $this->restoreTable('threads', function(object $obj, Connection $con) {
           // Restore thread comments
           $comments = DB::table('comments')->where('commentable_type', 'thread')->where('commentable_id', $obj->id)->get();
           foreach ($comments as $comment) {
               $con->table('comments')->insert((array)$comment);
           }
        });

        // Restore user comments
        $this->restoreTable('comments');

        // Restore user bans & cases
        $this->restoreTable('bans');
        $this->restoreTable('user_cases');

        // Restore user blocked/followed content
        $this->restoreTable('blocked_users');
        $this->restoreTable('blocked_tags');
        $this->restoreTable('followed_games');
        $this->restoreTable('followed_mods');
        $this->restoreTable('followed_users');
        $this->restoreTable('subscriptions');

        // Restore user likes
        $this->restoreTable('mod_likes');

        // Restore user mod membership
        $this->restoreTable('mod_members');

        // Restore user social logins
        $this->restoreTable('social_logins');

        // Restore user notifications
        $this->restoreTable('notifications');

        // Restore user supporter information
        $this->restoreTable('supporters');

        // Restore user roles
        $this->restoreTable('role_user');
        $this->restoreTable('game_role_user');

        $this->info("Restoring user...Done!");
    }
}
