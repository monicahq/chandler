<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ContactReminder;
use App\Models\ScheduledContactReminder;
use App\Models\UserNotificationChannel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserNotificationChannelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_user()
    {
        $channel = UserNotificationChannel::factory()->create();

        $this->assertTrue($channel->user()->exists());
    }
}
