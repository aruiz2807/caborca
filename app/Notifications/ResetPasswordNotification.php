<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $email = $notifiable->getEmailForPasswordReset();
        $user = User::where('email', $email)->first();
        $url = url("/reset-password/{$this->token}?email={$email}");

        return (new MailMessage)
            ->subject(Config::get('app.name') . ' - ' . trans('emails.reset_password_subject'))
            ->markdown('emails.auth.reset-password', [
                'user' => $user,
                'url' => $url,
            ]);
    }
}
