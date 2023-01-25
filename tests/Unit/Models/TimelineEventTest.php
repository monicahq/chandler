<?php

namespace Tests\Unit\Models;

use App\Models\TimelineEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TimelineEventTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_contact()
    {
        $timeline = TimelineEvent::factory()->create();

        $this->assertTrue($timeline->vault()->exists());
    }
}
