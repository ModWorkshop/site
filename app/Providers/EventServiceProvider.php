<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // ... other providers
            \SocialiteProviders\Steam\SteamExtendSocialite::class.'@handle',
            \SocialiteProviders\Discord\DiscordExtendSocialite::class.'@handle',
            \SocialiteProviders\GitHub\GitHubExtendSocialite::class.'@handle',
            \SocialiteProviders\GitLab\GitLabExtendSocialite::class.'@handle',
        ],
        'Illuminate\Auth\Events\Authenticated' => [
            'App\Listeners\UserAuthenticated'
        ],
        \Illuminate\Auth\Events\Login::class => [
            'App\Listeners\UserAuthenticated'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
