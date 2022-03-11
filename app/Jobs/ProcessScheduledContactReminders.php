<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Models\UserNotificationChannel;
use App\Models\ScheduledContactReminder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\Notifications\SendEmailNotification;

class ProcessScheduledContactReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // this cron job runs every five minutes, so we must make sure that
        // the date we run this cron against, has no seconds, otherwise getting
        // the scheduled reminders will not return any results
        $currentDate = Carbon::now();
        $currentDate->second = 0;

        $scheduledReminders = ScheduledContactReminder::where('scheduled_at', '<=', $currentDate)
            ->with('userNotificationChannel')
            ->get();

        foreach ($scheduledReminders as $scheduledReminder) {
            if ($scheduledReminder->userNotificationChannel->type == UserNotificationChannel::TYPE_EMAIL) {
                SendEmailNotification::dispatch($scheduledReminder)->onQueue('low');
            }
        }
    }
}
