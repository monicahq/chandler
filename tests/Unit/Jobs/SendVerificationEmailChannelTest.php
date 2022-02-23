<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use App\Jobs\CreateContactLog;
use App\Jobs\SendVerificationEmailChannel;
use App\Mail\UserNotificationChannelEmailCreated;
use App\Models\UserNotificationChannel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;

class SendVerificationEmailChannelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_logs_a_contact_log(): void
    {
        Mail::fake();

        $channel = UserNotificationChannel::factory()->create([
            'type' => UserNotificationChannel::TYPE_EMAIL,
            'content' => 'admin@admin.com',
        ]);
        SendVerificationEmailChannel::dispatch($channel);

        Mail::assertSent(UserNotificationChannelEmailCreated::class, function ($mail) use ($channel) {
            return $mail->hasTo('admin@admin.com');
        });
    }
}
