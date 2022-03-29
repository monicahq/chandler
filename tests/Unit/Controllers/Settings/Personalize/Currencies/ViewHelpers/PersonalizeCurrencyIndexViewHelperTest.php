<?php

namespace Tests\Unit\Controllers\Settings\Personalize\Currencies\ViewHelpers;

use function env;
use Tests\TestCase;
use App\Models\Account;
use App\Models\Currency;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Settings\Personalize\Currencies\ViewHelpers\PersonalizeCurrencyIndexViewHelper;

class PersonalizeCurrencyIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $currency = Currency::factory()->create();
        $account = Account::factory()->create();
        $account->currencies()->attach($currency->id, ['active' => true]);
        $array = PersonalizeCurrencyIndexViewHelper::data($account);
        $this->assertEquals(
            2,
            count($array)
        );
        $this->assertArrayHasKey('currencies', $array);
        $this->assertEquals(
            [
                'settings' => env('APP_URL').'/settings',
                'personalize' => env('APP_URL').'/settings/personalize',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_data_needed_for_the_data_transfer_object(): void
    {
        $currency = Currency::factory()->create();
        $account = Account::factory()->create();
        $account->currencies()->attach($currency->id, ['active' => true]);
        $array = PersonalizeCurrencyIndexViewHelper::dtoCurrency($currency);
        $this->assertEquals(
            [
                'id' => $currency->id,
                'code' => $currency->code,
                'name' => 'Canadian Dollar',
                'active' => true,
                'url' => [
                    'update' => env('APP_URL').'/settings/personalize/currencies/'.$currency->id,
                ],
            ],
            $array
        );
    }
}
