<?php

namespace Tests\Unit\Jobs\Notifications;

use App\Jobs\Notifications\SendEmailNotification;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Models\UserNotificationChannel;
use App\Jobs\SendVerificationEmailChannel;
use App\Mail\SendReminder;
use App\Mail\UserNotificationChannelEmailCreated;
use App\Models\ContactReminder;
use App\Models\ScheduledContactReminder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SendEmailNotificationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sends_a_notification_by_email(): void
    {
        Mail::fake();

        $channel = UserNotificationChannel::factory()->create([
            'type' => UserNotificationChannel::TYPE_EMAIL,
            'content' => 'admin@admin.com',
        ]);
        $scheduledContactReminder = ScheduledContactReminder::factory()->create([
            'user_notification_channel_id' => $channel->id,
        ]);

        SendEmailNotification::dispatch($scheduledContactReminder);

        Mail::assertSent(SendReminder::class);
    }
}
