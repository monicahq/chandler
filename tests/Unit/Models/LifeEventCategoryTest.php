<?php

namespace Tests\Unit\Models;

use App\Models\LifeEventCategory;
use App\Models\LifeEventType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LifeEventCategoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_an_account()
    {
        $lifeEventCategory = LifeEventCategory::factory()->create();
        $this->assertTrue($lifeEventCategory->account()->exists());
    }

    /** @test */
    public function it_has_many_types()
    {
        $lifeEventCategory = LifeEventCategory::factory()->create();
        LifeEventType::factory(2)->create([
            'life_event_category_id' => $lifeEventCategory->id,
        ]);

        $this->assertTrue($lifeEventCategory->lifeEventTypes()->exists());
    }
}
