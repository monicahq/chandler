<?php

namespace App\Notifications;

use App\Helpers\NameHelper;
use App\Models\ScheduledContactReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendEmailReminder extends Notification
{
    use Queueable;

    private ScheduledContactReminder $scheduledReminder;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ScheduledContactReminder $scheduledReminder)
    {
        $this->scheduledReminder = $scheduledReminder;
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
        $reminder = $this->scheduledReminder->reminder;
        $contact = $reminder->contact;
        $user = $this->scheduledReminder->userNotificationChannel->user;
        $name = NameHelper::formatContactName($user, $contact);
        $url = route('contact.show', [
            'vault' => $contact->vault_id,
            'contact' => $contact->id,
        ]);

        return (new MailMessage)
            ->subject('Reminder about ' . $name)
            ->greeting('Hi!')
            ->line('Reminder about '.$name.'.')
            ->line($reminder->label)
            ->action('View contact', $url)
            ->line('Thank you for using Monica.');
    }
}
