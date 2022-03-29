<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Account;
use App\Models\Currency;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CurrencyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_many_accounts(): void
    {
        $account = Account::factory()->create();
        $currency = Currency::factory()->create();

        $currency->accounts()->sync([$account->id => ['active' => true]]);

        $this->assertTrue($currency->accounts()->exists());
    }
}
