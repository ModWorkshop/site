<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Request;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $prefix = env('FRONT_URL')."/verify-email";
            return (new MailMessage)
                ->line('Welcome to ModWorkshop!')
                ->line("In order to activate your account you must verify this email address.")
                ->line("If you did not initiate it, you can safely ignore this message and the account will be automatically deleted after a day.")
                ->action('Verify', $prefix.str_replace(env('APP_URL').'/email/verify', '', $url));
        });

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return env('FRONT_URL').'/reset-password/'.$token;
        });
    }
}
