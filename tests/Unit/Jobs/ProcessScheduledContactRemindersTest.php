<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Models\User;
use App\Jobs\CreateAuditLog;
use App\Jobs\Notifications\SendEmailNotification;
use App\Jobs\ProcessScheduledContactReminders;
use App\Models\ContactReminder;
use App\Models\ScheduledContactReminder;
use App\Models\UserNotificationChannel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;

class ProcessScheduledContactRemindersTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_processes_all_the_scheduled_contact_reminders(): void
    {
        Bus::fake();

        Carbon::setTestNow(Carbon::create(2018, 1, 1, 0, 0, 0));

        $contactReminder = ContactReminder::factory()->create([
            'type' => UserNotificationChannel::TYPE_EMAIL,
        ]);

        ScheduledContactReminder::factory()->create([
            'contact_reminder_id' => $contactReminder->id,
            'scheduled_at' => Carbon::now(),
        ]);

        $job = new ProcessScheduledContactReminders();
        $job->dispatch();
        $job->handle();

        Bus::assertDispatched(SendEmailNotification::class);
    }
}
