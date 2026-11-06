<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Reset Your UNCART Password')
            ->view('emails.password-reset', [
                'resetUrl' => $resetUrl,
                'userName' => $notifiable->name ?? 'Valued Customer',
                'userEmail' => $notifiable->getEmailForPasswordReset(),
                'expiresIn' => config('auth.passwords.users.expire', 60),
            ]);
    }
}
