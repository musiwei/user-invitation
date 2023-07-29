<?php

namespace Musiwei\UserInvitation\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationNotification extends Notification
{
    use Queueable;

    protected string $notificationUrl;
    protected int $validHours;

    /**
     * Create a new notification_url instance.
     *
     * @param string $notificationUrl
     * @param int $validHours
     */
    public function __construct(string $notificationUrl, int $validHours)
    {
        $this->notificationUrl = $notificationUrl;
        $this->validHours = $validHours;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__("You are invited to register ") . config('app.name'))
            ->greeting(__('Welcome onboard! '))
            ->line(__('Click below button to join ') . config('app.name') . ". ")
            ->action(__('Accept'), $this->notificationUrl)
            ->line(__('This link will expire after ') . $this->validHours . __(' hours. '));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray(mixed $notifiable): array
    {
        return [];
    }
}
