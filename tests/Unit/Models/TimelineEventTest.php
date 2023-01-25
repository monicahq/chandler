<?php

namespace Tests\Unit\Models;

use App\Models\Contact;
use App\Models\LifeEvent;
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

    /** @test */
    public function it_has_many_life_events(): void
    {
        $timeline = TimelineEvent::factory()->create();
        LifeEvent::factory()->count(2)->create([
            'timeline_event_id' => $timeline->id,
        ]);

        $this->assertTrue($timeline->lifeEvents()->exists());
    }
}
