<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    'steam' => [
        'realm' => preg_replace("(^https?://)", '', env('FRONTEND_URL')),
        'client_id' => env('APP_NAME'),
        'client_secret' => env('STEAM_CLIENT_SECRET'),
        'redirect' => env('SOCIALITE_REDIRECT_URL').'steam',
    ],

    'discord' => [    
        'client_id' => env('DISCORD_CLIENT_ID'),  
        'client_secret' => env('DISCORD_CLIENT_SECRET'),  
        'redirect' => env('SOCIALITE_REDIRECT_URL').'discord',
        'allow_gif_avatars' => (bool)env('DISCORD_AVATAR_GIF', true),
        'avatar_default_extension' => env('DISCORD_EXTENSION_DEFAULT', 'webp'), // only pick from jpg, png, webp
    ],

    'github' => [    
        'client_id' => env('GITHUB_CLIENT_ID'),  
        'client_secret' => env('GITHUB_CLIENT_SECRET'),  
        'redirect' => env('SOCIALITE_REDIRECT_URL').'github'
    ],

    'gitlab' => [    
        'client_id' => env('GITLAB_CLIENT_ID'),  
        'client_secret' => env('GITLAB_CLIENT_SECRET'),  
        'redirect' => env('SOCIALITE_REDIRECT_URL').'gitlab'
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
