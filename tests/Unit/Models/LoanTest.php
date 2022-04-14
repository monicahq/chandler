<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Loan;
use App\Models\Contact;
use App\Models\Currency;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoanTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_currency()
    {
        $currency = Currency::factory()->create();
        $loan = Loan::factory()->create([
            'currency_id' => $currency->id,
        ]);

        $this->assertTrue($loan->currency()->exists());
    }

    /** @test */
    public function it_has_many_contacts_as_loaners(): void
    {
        $ross = Contact::factory()->create([]);
        $monica = Contact::factory()->create();
        $loan = Loan::factory()->create();

        $loan->loaners()->sync([$ross->id => ['loanee_id' => $monica->id]]);

        $this->assertTrue($loan->loaners()->exists());
    }

    /** @test */
    public function it_has_many_contacts_as_loanees(): void
    {
        $ross = Contact::factory()->create([]);
        $monica = Contact::factory()->create();
        $loan = Loan::factory()->create();

        $loan->loanees()->sync([$ross->id => ['loaner_id' => $monica->id]]);

        $this->assertTrue($loan->loanees()->exists());
    }
}
