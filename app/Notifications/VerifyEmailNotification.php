<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;

class VerifyEmailNotification extends VerifyEmail
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(Config::get('app.name') . ' - ' . trans('emails.verify_email_subject'))
            ->markdown('emails.auth.verify-email', [
                'url' => $url,
                'user' => $notifiable,
            ]);
    }
}
