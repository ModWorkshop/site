<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Log;
use Request;
use URL;

class EmailVerificationNotification extends VerifyEmail
{
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

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
            ->line('Welcome to ModWorkshop!')
            ->line("In order to activate your account you must verify this email address.")
            ->line("If you did not initiate it, you can safely ignore this message and the account will be automatically deleted after a day.")
            ->action('Verify', $prefix . preg_replace('/https?:\/\/.+\/email\/verify/', '', $url));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
