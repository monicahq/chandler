<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ContactReminder;
use App\Models\ScheduledContactReminder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContactReminderTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_contact()
    {
        $reminder = ContactReminder::factory()->create();

        $this->assertTrue($reminder->contact()->exists());
    }

    /** @test */
    public function it_has_many_users(): void
    {
        $reminder = ContactReminder::factory()->create();
        $user = User::factory()->create();
        $reminder->users()->sync([$user->id => ['scheduled_at' => Carbon::now()]]);

        $this->assertTrue($reminder->users()->exists());
    }

    /** @test */
    public function it_has_many_scheduled_reminders(): void
    {
        $reminder = ContactReminder::factory()->create();
        ScheduledContactReminder::factory()->count(2)->create([
            'contact_reminder_id' => $reminder->id,
        ]);

        $this->assertTrue($reminder->scheduledContactReminders()->exists());
    }
}
