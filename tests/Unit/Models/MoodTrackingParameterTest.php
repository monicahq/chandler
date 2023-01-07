<?php

namespace Tests\Unit\Models;

use App\Models\MoodTrackingParameter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MoodTrackingParameterTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_vault()
    {
        $parameter = MoodTrackingParameter::factory()->create();

        $this->assertTrue($parameter->vault()->exists());
    }
}
