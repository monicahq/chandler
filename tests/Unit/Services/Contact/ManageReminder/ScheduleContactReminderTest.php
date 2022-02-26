<?php

namespace Tests\Unit\Services\Contact\ManageReminder;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vault;
use App\Models\Account;
use App\Models\Contact;
use App\Jobs\CreateAuditLog;
use App\Jobs\CreateContactLog;
use App\Models\ContactReminder;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\UserNotificationChannel;
use App\Services\Contact\ManageReminder\CreateReminder;
use App\Services\Contact\ManageReminder\ScheduleContactReminder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ScheduleContactReminderTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_schedules_a_reminder_of_date_in_the_past_in_utc(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));

        $regis = $this->createUser();
        $regis->timezone = 'UTC';
        $regis->save();

        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);

        $contactReminder = ContactReminder::factory()->create([
            'contact_id' => $contact->id,
            'type' => ContactReminder::TYPE_ONE_TIME,
            'day' => 2,
            'month' => 10,
            'year' => 2000,
        ]);
        $channel = UserNotificationChannel::factory()->create([
            'user_id' => $regis->id,
            'preferred_time' => '18:00',
        ]);

        $this->executeService($contactReminder, $channel, '2018-10-02 18:00:00');
    }

    /** @test */
    public function it_schedules_a_reminder_of_date_in_the_past_in_another_timezone(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));

        $regis = $this->createUser();
        $regis->timezone = 'America/New_York';
        $regis->save();

        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);

        $contactReminder = ContactReminder::factory()->create([
            'contact_id' => $contact->id,
            'type' => ContactReminder::TYPE_ONE_TIME,
            'day' => 2,
            'month' => 10,
            'year' => 2000,
        ]);
        $channel = UserNotificationChannel::factory()->create([
            'user_id' => $regis->id,
            'preferred_time' => '18:00',
        ]);

        $this->executeService($contactReminder, $channel, '2018-10-02 22:00:00');
    }

    /** @test */
    public function it_schedules_a_reminder_of_date_in_the_past_in_another_timezone_again(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));

        $regis = $this->createUser();
        $regis->timezone = 'Asia/Sakhalin';
        $regis->save();

        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);

        $contactReminder = ContactReminder::factory()->create([
            'contact_id' => $contact->id,
            'type' => ContactReminder::TYPE_ONE_TIME,
            'day' => 2,
            'month' => 10,
            'year' => 2000,
        ]);
        $channel = UserNotificationChannel::factory()->create([
            'user_id' => $regis->id,
            'preferred_time' => '18:00',
        ]);

        $this->executeService($contactReminder, $channel, '2018-10-02 07:00:00');
    }

    //public function it_schedules_a_reminder_of_date_in_the_future_in_utc(): void

    //public function it_doesnt_schedule_a_reminder_if_no_user_notification_channel_is_defined(): void


    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new ScheduleContactReminder)->execute($request);
    }

    private function executeService(ContactReminder $reminder, UserNotificationChannel $channel, string $expectedDate): void
    {
        $request = [
            'contact_reminder_id' => $reminder->id,
        ];

        (new ScheduleContactReminder)->execute($request);

        $this->assertDatabaseHas('scheduled_contact_reminders', [
            'user_notification_channel_id' => $channel->id,
            'triggered_at' => $expectedDate,
        ]);
    }
}
