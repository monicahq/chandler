<?php

namespace App\Notifications;

use App\Helpers\NameHelper;
use App\Models\ContactReminder;
use App\Models\User;
use App\Models\UserNotificationChannel;
use App\Models\UserNotificationSent;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReminderTriggered extends Notification
{
    use Queueable;

    private ContactReminder $contactReminder;
    private User $user;
    private UserNotificationChannel $channel;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ContactReminder $contactReminder, UserNotificationChannel $channel)
    {
        $this->contactReminder = $contactReminder;
        $this->channel = $channel;
        $this->user = $channel->user;
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
        $contact = $this->contactReminder->contact;
        $contactName = NameHelper::formatContactName($this->user, $contact);

        UserNotificationSent::create([
            'user_notification_channel_id' => $this->channel->id,
            'sent_at' => Carbon::now(),
            'subject_line' => $this->contactReminder->label,
        ]);

        return (new MailMessage())
                    ->subject(trans('email.notification_reminder_email', ['name' => $contactName]))
                    ->line(trans('email.reminder_triggered_intro'))
                    ->line($this->contactReminder->label)
                    ->line(trans('email.reminder_triggered_for'))
                    ->line($contactName)
                    ->line(trans('email.reminder_triggered_signature'));
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
