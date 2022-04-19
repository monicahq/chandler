<?php

namespace Tests\Unit\Domains\Settings\ManageCurrencies\Web\ViewHelpers;

use Tests\TestCase;
use App\Models\Account;
use App\Models\Currency;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Settings\ManageCurrencies\Web\ViewHelpers\CurrencyIndexViewHelper;

class CurrencyIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $currency = Currency::factory()->create();
        $account = Account::factory()->create();
        $account->currencies()->attach($currency->id, ['active' => true]);
        $collection = CurrencyIndexViewHelper::data($account);
        $this->assertEquals(
            [
                0 => [
                    'id' => 165,
                    'name' => 'CAD',
                ],
            ],
            $collection->toArray()
        );
    }
}
