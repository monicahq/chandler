<?php

namespace Tests\Unit\Services\User\NotificationChannels;

use Tests\TestCase;
use App\Models\User;
use App\Jobs\CreateAuditLog;
use App\Mail\TestEmailSent;
use Illuminate\Support\Facades\Queue;
use App\Models\UserNotificationChannel;
use App\Services\User\NotificationChannels\SendTestEmail;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\User\NotificationChannels\ToggleUserNotificationChannel;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendTestEmailTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sends_a_test_email(): void
    {
        $ross = $this->createUser();
        $channel = UserNotificationChannel::factory()->create([
            'user_id' => $ross->id,
            'content' => 'admin@admin.com',
            'type' => UserNotificationChannel::TYPE_EMAIL,
        ]);
        $this->executeService($ross, $channel);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new SendTestEmail)->execute($request);
    }

    /** @test */
    public function it_fails_if_notification_channel_doesnt_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $channel = UserNotificationChannel::factory()->create();
        $this->executeService($ross, $channel);
    }

    /** @test */
    public function it_fails_if_notification_channel_type_is_not_email(): void
    {
        $this->expectException(Exception::class);

        $ross = $this->createUser();
        $channel = UserNotificationChannel::factory()->create([
            'user_id' => $ross->id,
            'content' => 'admin@admin.com',
            'type' => 'slack',
        ]);
        $this->executeService($ross, $channel);
    }

    private function executeService(User $author, UserNotificationChannel $channel): void
    {
        Mail::fake();

        $request = [
            'account_id' => $author->account_id,
            'author_id' => $author->id,
            'user_notification_channel_id' => $channel->id,
        ];

        $channel = (new SendTestEmail)->execute($request);

        $this->assertInstanceOf(
            UserNotificationChannel::class,
            $channel
        );

        Mail::assertSent(TestEmailSent::class, function ($mail) {
            return $mail->hasTo('admin@admin.com');
        });
    }
}
