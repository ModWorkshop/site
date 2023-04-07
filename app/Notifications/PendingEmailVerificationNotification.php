<?php

namespace App\Notifications;

use Carbon\Carbon;
use Config;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Log;
use URL;

class PendingEmailVerificationNotification extends VerifyEmail
{
    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $prefix = env('FRONTEND_URL')."/verify-email";
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('New email verification!')
            ->line("You've requested to change your email address.")
            ->line("To do so, you must first verify your new email address.")
            ->line("If you did not initiate it, you can safely ignore this message.")
            ->action('Verify', $prefix . preg_replace('/https?:\/\/.+\/email\/verify/', '', $verificationUrl));
    }

    protected function verificationUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->pending_email),
            ]
        );
    }
}
