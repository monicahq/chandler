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

    /** @test */
    public function it_has_one_loaner()
    {
        $loan = Loan::factory()->create();

        $this->assertTrue($loan->loaner()->exists());
    }

    /** @test */
    public function it_has_one_loanee()
    {
        $loan = Loan::factory()->create();

        $this->assertTrue($loan->loanee()->exists());
    }
}
