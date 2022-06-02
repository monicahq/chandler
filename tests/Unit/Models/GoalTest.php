<?php

namespace Tests\Unit\Models;

use App\Models\Goal;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_contact()
    {
        $goal = Goal::factory()->create();

        $this->assertTrue($goal->contact()->exists());
    }
}
