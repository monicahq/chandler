<?php

namespace Tests\Unit\Models;

use App\Models\ContactTask;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CallTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_contact()
    {
        $task = ContactTask::factory()->create();

        $this->assertTrue($task->contact()->exists());
    }

    /** @test */
    public function it_has_one_author()
    {
        $task = ContactTask::factory()->create();

        $this->assertTrue($task->author()->exists());
    }

    /** @test */
    public function it_has_one_call_reason()
    {
        $task = ContactTask::factory()->create();

        $this->assertTrue($task->callReason()->exists());
    }
}
