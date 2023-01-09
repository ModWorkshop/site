<?php

namespace App\Services;

use App\Models\User;
use Arr;

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
     *
     * @param string $webHook
     * @param string $message
     * @param array $args
     * @return void
     */
    static function sendDiscordMessage(string $webHook, string $message, array $args=[]){
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
        $handle = curl_init($webHook);
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

    /**
     * Collects permissions into a nice hash table
     */
    public static function collectPermissions($roles)
    {
        $permissions = [];
        foreach ($roles as $role) {
            if (!$role->is_vanity && $role->relationLoaded('permissions')) {
                foreach ($role->permissions as $perm) {
                    $permissions[$perm->name] = true;
                }
            }
        }

        return $permissions;
    }

    /**
     * Returns a unique name for a user
     */
    public static function getUniqueName(string $name): string
    {
        $uniqueName ??= $name;
        $uniqueName = preg_replace('([^a-zA-Z0-9-_])', '', strtolower($uniqueName));
        $users = User::where('unique_name', 'ILIKE', $uniqueName.'%')->get();

        //Try to make a unique name for the user
        $num = null;
        $found = false;
        while(!$found) {
            $current = $uniqueName.$num;
            if (!Arr::first($users, fn($val) => strtolower($val->unique_name) === $current)) {
                $uniqueName = $current;
                $found = true;
            } else {
                $num ??= 0;
                $num++;
            }
        }

        return $uniqueName;
    }
}