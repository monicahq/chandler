<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Loan;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoanTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_contact()
    {
        $loan = Loan::factory()->create();

        $this->assertTrue($loan->contact()->exists());
    }
}
