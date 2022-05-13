<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ContactTask;
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
