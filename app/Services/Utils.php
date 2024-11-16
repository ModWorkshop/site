<?php

namespace App\Services;

use App\Models\Ban;
use App\Models\Report;
use App\Models\User;
use App\Models\UserCase;
use App\Models\UserRecord;
use Arr;
use Auth;
use Carbon\Carbon;

const bbCodeConversion = [
    "[b]" => "**",
    "[/b]" => "**",
    "[u]" => "__",
    "[/u]" => "__",
    "[s]" => "~~",
    "[/s]" => "~~",
    "[i]" => "*",
    "[/i]" => "*"
];

class Utils {
    /**
     * Send a message to Discord via a webhook
     * Handles some form of markdown escaping to avoid webhook mentioning users (only args, careful with the message!)
     */
    static function sendDiscordMessage(string|array $webhook, string $message, array $args=[]){
        //BB-Code to Discord formating

        foreach ($args as $i => $v) {
            //Escape @
            $v = preg_replace('/(@)/', '@​', $v); //This contains a zero width space.
            //Escape markdown
            $args[$i] = preg_replace('/(\*|\\\|_|@|`|~|>|\|)/', '\\\\$0', $v);
        }

        $message = strtr(sprintf($message, ...$args), bbCodeConversion);

        //Assemble the Data
        $assemble = ['content' => $message];

        //Convert the data to Json
        $data_string = json_encode($assemble);

        //Build the Curl request and its settings.
        if (!is_array($webhook)) {
            $webHook = [$webhook];
        }
        foreach ($webhook as $wb) {
            $handle = curl_init($wb);
            if ($handle) {
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($handle, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: '.strlen($data_string)]);

                //Execute CURL
                curl_exec($handle);
                curl_close($handle);
            }
        }
	}

    /**
     * Collects permissions into a nice hash table
     */
    public static function collectPermissions($roles)
    {
        $permissions = [];
        foreach ($roles as $role) {
            if (!$role->is_vanity && $role->relationLoaded('permissions')) {
                foreach ($role->cachedPermissions as $perm) {
                    $permissions[$perm->name] = true;
                }
            }
        }

        return $permissions;
    }

    /**
     * Returns a unique name for a user
     */
    public static function getUniqueName(string $name, bool $cacheUsers=false): string
    {
        $uniqueName ??= $name;
        $uniqueName = preg_replace('([^a-zA-Z0-9-_])', '', strtolower($uniqueName));
        if (strlen($uniqueName) == 0) {
            $uniqueName = 'mws';
        }
        $foundUsers = User::where('unique_name', 'ILIKE', $uniqueName.'%')->get();

        //Try to make a unique name for the user
        $num = null;
        $found = false;
        while(!$found) {
            $current = $uniqueName.$num;
            if (!Arr::first($foundUsers, fn($val) => strtolower($val->unique_name) === $current)) {
                $uniqueName = $current;
                $found = true;
            } else {
                $num ??= 0;
                $num++;
            }
        }

        return $uniqueName;
    }

    /**
     * Returns a unique name for a user (DO NOT USE, only migration code)
     */
    public static function getUniqueNameCached(string $name): string
    {
        static $used = [];

        $uniqueName ??= $name;
        $uniqueName = preg_replace('([^a-zA-Z0-9-_])', '', strtolower($uniqueName));
        if (strlen($uniqueName) == 0) {
            $uniqueName = 'mws';
        }

        //Try to make a unique name for the user
        $num = null;
        $found = false;
        while(!$found) {
            $current = $uniqueName.(isset($num) ? '-'.$num : '');
            if (!isset($used[$current])) {
                $used[$current] = true;
                return $current;
            } else {
                $num ??= 0;
                $num++;
            }
        }
    }

    /**
     * Restores user's bans, warnings and social logins in order to prevent users from (too easily) evading bans.
     * Why the too easy? Obviously, it's not too hard to fool the system and it's impossible to have a perfect system.
     */
    public static function partlyRestoreUser(UserRecord $record, int $newUserId)
    {
        //Pass all saved bans and warnings to the new users if they deleted their account.
        if (isset($record) && !User::where('id', $record->user_id)->exists()) {
            Ban::where('user_id', $record->user_id)->update([
                'user_id' => $newUserId,
            ]);
            UserCase::where('user_id', $record->user_id)->update([
                'user_id' => $newUserId,
            ]);
            Report::where('reported_user_id', $record->user_id)->update([
                'reported_user_id' => $newUserId,
            ]);
            $record->delete();
        }
    }

    // Stupidity - https://github.com/laravel/framework/issues/1841
    public static function convertToUTC(array &$val, string $key)
    {
        if (isset($val[$key])) {
            $val[$key] = Carbon::parse($val[$key])->utc()->toDateTimeString();
        }
    }

    public static function forumCategoriesFilter($q, bool $thread=false)
    {
        $user = Auth::user();
        if (isset($user) && $user->hasPermission('manage-discussions')) {
            return;
        }

        $roleIds = [1];
        $gameRoleIds = null;

        if (isset($user)) {
            $roleIds = [1, ...Arr::pluck($user->roles, 'id')];
            $gameRoleIds = Arr::pluck($user->allGameRoles, 'id');
        }

        if ($thread) {
            $q->where('private_threads', false);
        }

        $q->where(function($q) use ($roleIds, $gameRoleIds) {
            $q->where('is_private', true)->where(fn($q) =>
                $q->whereHasIn('roles', fn($q) => $q->where('can_view', true)->whereIn('role_id', $roleIds))
                ->when(isset($gameRoleIds))->orWhereHasIn('gameRoles', fn($q) => $q->where('can_view', true)->whereIn('role_id', $gameRoleIds))
            )->orWhere('is_private', false)->where(fn($q) =>
                $q->whereDoesntHaveIn('roles', fn($q) => $q->where('can_view', false)->whereIn('role_id', $roleIds))
                ->when(isset($gameRoleIds))->whereDoesntHaveIn('gameRoles', fn($q) => $q->where('can_view', false)->whereIn('role_id', $gameRoleIds))
            );
        });
    }

    /**
     * Removes unwanted characters from a file name. This also removes repeating period characters.
     *
     * @param string $name The name
     * @return string
     */
    public static function safeFileName(string $name) {
        if (empty($name)) {
            return '';
        }
        return preg_replace('/[^A-Za-z0-9\s\-_]/', '', explode('.', $name)[0]);
    }

    /**
     * Returns a safe file type. For example if you have x.y.z.w you'll receive y.z.w
     *
     * @param string $name
     * @param integer $limit
     * @return string
     */
    public static function safeFileType(string $name, int $limit = 2) {
        if (empty($name)) {
            return '';
        }

        $split = explode('.', $name); // Spllit by dot
        $split = array_slice($split, 1); // Cut file name
        $split = array_map(fn($str) => preg_replace('/[^A-Za-z0-9\-_]/', '', $str), $split); // Remove invalid characters
        $split = array_filter($split); // Remove empties
        $split = array_slice($split, -$limit, $limit); // Take the last part using limit

        return implode('.', $split); // Return string joined by dot
    }
}
