<?php

namespace Musiwei\UserInvitation\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationNotification extends Notification
{
    use Queueable;
    protected $notificationUrl;
    protected $validHours;
    /**
     * Create a new notification_url instance.
     *
     * @param $notification_url
     */
    public function __construct($notificationUrl, $validMinutes)
    {
        $this->notificationUrl = $notificationUrl;
        $this->validHours = $validMinutes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('user-invitation.subject')
            ->greeting(__('user-invitation.welcome'))
            ->line(__('user-invitation.invited', ['name' => config('app.name')]))
            ->action(__('user-invitation.accept'), $this->notificationUrl)
            ->line(__('user-invitation.expiry', ['hour' => $this->validHours]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
