<?php

namespace App\Jobs;

use App\Mail\UserNotificationChannelEmailCreated;
use App\Models\User;
use App\Models\UserNotificationChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendVerificationEmailChannel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected UserNotificationChannel $channel;

    /**
     * Create a new job instance.
     *
     * @param UserNotificationChannel $channel
     * @return void
     */
    public function __construct(UserNotificationChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->channel->type !==  UserNotificationChannel::TYPE_EMAIL) {
            return;
        }

        Mail::to($this->channel->content)
            ->send(new UserNotificationChannelEmailCreated($this->channel)
        );
    }
}
