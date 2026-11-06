<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RegistrationOtpNotification extends Notification
{
    use Queueable;

    public string $otp;
    public string $userName;
    public string $role;

    public function __construct(string $otp, string $userName = 'Valued Member', string $role = 'customer')
    {
        $this->otp = $otp;
        $this->userName = $userName;
        $this->role = $role;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $roleTitle = $this->role === 'vendor' ? 'Seller Account' : 'Customer Account';

        return (new MailMessage)
            ->subject("Your UNCART Registration Verification Code: {$this->otp}")
            ->view('emails.registration-otp', [
                'otp' => $this->otp,
                'userName' => $this->userName,
                'roleTitle' => $roleTitle,
                'expiresIn' => 15,
            ]);
    }
}
