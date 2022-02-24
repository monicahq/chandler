<?php

namespace Tests\Unit\Controllers\Settings\Notifications\Logs\ViewHelpers;

use function env;

use App\Http\Controllers\Settings\Notifications\Logs\ViewHelpers\NotificationsLogIndexViewHelper;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserNotificationChannel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\UserNotificationSent;

class NotificationsLogIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $user = User::factory()->create();
        $channel = UserNotificationChannel::factory()->create([
            'user_id' => $user->id,
        ]);
        UserNotificationSent::factory()->create([
            'user_notification_channel_id' => $channel->id,
            'sent_at' => '2020-01-01 00:00:00',
            'subject_line' => 'subject line',
        ]);

        $array = NotificationsLogIndexViewHelper::data($channel, $user);

        $this->assertEquals(
            2,
            count($array)
        );

        $this->assertArrayHasKey('notifications', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            [
                'settings' => env('APP_URL').'/settings',
                'channels' => env('APP_URL').'/settings/notifications',
                'back' => env('APP_URL').'/settings',
            ],
            $array['url']
        );
    }
}
