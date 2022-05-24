<?php

namespace Tests\Unit\Models;

use App\Models\LifeEvent;
use App\Models\LifeEventType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LifeEventTypeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_a_life_event_category()
    {
        $lifeEventType = LifeEventType::factory()->create();
        $this->assertTrue($lifeEventType->lifeEventCategory()->exists());
    }

    /** @test */
    public function it_has_many_types()
    {
        $lifeEventType = LifeEventType::factory()->create();
        LifeEvent::factory(2)->create([
            'life_event_type_id' => $lifeEventType->id,
        ]);

        $this->assertTrue($lifeEventType->lifeEvents()->exists());
    }
}
