<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\ContactReminder;
use App\Models\ContactTask;
use App\Models\UserNotificationChannel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContactTaskTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_contact()
    {
        $task = ContactTask::factory()->create();

        $this->assertTrue($task->contact()->exists());
    }
}
