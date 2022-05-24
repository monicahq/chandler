<?php

namespace Tests\Unit\Models;

use App\Models\LifeEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LifeEventTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_type()
    {
        $lifeEvent = LifeEvent::factory()->create();

        $this->assertTrue($lifeEvent->lifeEventType()->exists());
    }
}
