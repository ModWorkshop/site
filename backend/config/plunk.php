<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Plunk API Key
    |--------------------------------------------------------------------------
    |
    | This is your Plunk API key which you can find in your Plunk dashboard.
    | This key is used to authenticate with the Plunk API.
    |
    */
    'api_key' => env('PLUNK_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Plunk API URL
    |--------------------------------------------------------------------------
    |
    | This is the base URL for the Plunk API. For the official Plunk service,
    | this should be 'https://api.useplunk.com'. For self-hosted instances,
    | it will be your domain, for example 'https://plunk.yourdomain.com'.
    |
    */
    'api_url' => env('PLUNK_API_URL', 'https://api.useplunk.com'),

    /*
    |--------------------------------------------------------------------------
    | Plunk API Endpoint
    |--------------------------------------------------------------------------
    |
    | This is the endpoint for sending emails. For the official Plunk service,
    | this should be '/v1/send'. For self-hosted instances, it might be
    | different, for example '/api/v1/send'.
    |
    */
    'endpoint' => env('PLUNK_API_ENDPOINT', '/v1/send'),
];