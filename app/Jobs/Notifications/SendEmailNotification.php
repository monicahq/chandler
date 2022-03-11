<?php

namespace App\Jobs\Notifications;

use App\Mail\SendReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Models\ScheduledContactReminder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ScheduledContactReminder $scheduledReminder;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ScheduledContactReminder $scheduledReminder)
    {
        $this->scheduledReminder = $scheduledReminder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $emailAddress = $this->scheduledReminder->userNotificationChannel->content;
        $user = $this->scheduledReminder->userNotificationChannel->user;

        Mail::to($emailAddress)
            ->queue(new SendReminder($this->scheduledReminder, $user));
    }
}
