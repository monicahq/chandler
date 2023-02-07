<?php

namespace App\Notifications;

use Carbon\Carbon;
use App\Models\User;
use App\Helpers\DateHelper;
use Illuminate\Bus\Queueable;
use App\Models\ExportJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ExportAccountDone extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var ExportJob
     */
    public $exportJob;

    /**
     * Create a new message instance.
     *
     * @param  ExportJob  $exportJob
     * @return void
     */
    public function __construct(ExportJob $exportJob)
    {
        $this->exportJob = $exportJob->withoutRelations();
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  User  $user
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(User $user): MailMessage
    {
        $date = Carbon::parse($this->exportJob->created_at)
            ->setTimezone($user->timezone);

        return (new MailMessage)
            ->success()
            ->subject(__('Your export is ready'))
            ->greeting(__('Hello :username', ['username' => $user->first_name]))
            ->line(__('You requested a data export on :date. It is now ready to download.', ['date' => DateHelper::formatDate($date)]))
            ->action(__('Download export'), route('settings.export.index'));
    }
}
