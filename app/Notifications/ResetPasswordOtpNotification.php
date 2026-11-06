<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordOtpNotification extends Notification
{
    use Queueable;

    public string $otp;

    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your UNCART Password Reset Code: {$this->otp}")
            ->view('emails.password-reset-otp', [
                'otp' => $this->otp,
                'userName' => $notifiable->name ?? 'Valued Customer',
                'userEmail' => $notifiable->email,
                'expiresIn' => 15,
            ]);
    }
}
