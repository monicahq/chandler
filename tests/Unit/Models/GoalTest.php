<?php

namespace Tests\Unit\Models;

use App\Models\Contact;
use App\Models\Currency;
use App\Models\Goal;
use App\Models\Loan;
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
