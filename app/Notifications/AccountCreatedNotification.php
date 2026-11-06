<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;
use App\Models\Vendor;

class AccountCreatedNotification extends Notification
{
    use Queueable;

    public ?User $user;
    public ?Vendor $vendor;
    public array $extraDetails;

    public function __construct(?User $user = null, ?Vendor $vendor = null, array $extraDetails = [])
    {
        $this->user = $user;
        $this->vendor = $vendor;
        $this->extraDetails = $extraDetails;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $user = $this->user ?? $notifiable;
        $role = $user->role ?? 'customer';

        $roleName = match ($role) {
            'vendor' => 'Certified Merchant Seller',
            'admin' => 'Platform Administrator',
            default => 'Customer Member',
        };

        $subject = ($role === 'vendor')
            ? "Welcome to UNCART — Your Merchant Account & Store Are Ready!"
            : "Welcome to UNCART — Your Account Has Been Created!";

        $actionUrl = ($role === 'vendor')
            ? route('vendor.dashboard')
            : route('profile.index');

        $actionText = ($role === 'vendor')
            ? 'Open Seller Dashboard'
            : 'View Your Profile & Start Shopping';

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.account-created', [
                'user' => $user,
                'vendor' => $this->vendor,
                'roleName' => $roleName,
                'actionUrl' => $actionUrl,
                'actionText' => $actionText,
                'extraDetails' => $this->extraDetails,
            ]);
    }
}
