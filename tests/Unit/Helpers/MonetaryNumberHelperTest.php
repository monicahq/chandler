<?php

namespace Tests\Unit\Helpers;

use App\Helpers\MonetaryNumberHelper;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class MonetaryNumberHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_number_according_to_the_user_preference(): void
    {
        $number = 1234567;
        $user = User::factory()->create([
            'number_format' => User::NUMBER_FORMAT_TYPE_COMMA_THOUSANDS_DOT_DECIMAL,
        ]);
        $this->assertEquals(
            '12,345.67',
            MonetaryNumberHelper::formatValue($user, $number)
        );

        $user = User::factory()->create([
            'number_format' => User::NUMBER_FORMAT_TYPE_SPACE_THOUSANDS_COMMA_DECIMAL,
        ]);
        $this->assertEquals(
            '12 345,67',
            MonetaryNumberHelper::formatValue($user, $number)
        );

        $user = User::factory()->create([
            'number_format' => User::NUMBER_FORMAT_TYPE_DOT_THOUSANDS_COMMA_DECIMAL,
        ]);
        $this->assertEquals(
            '12.345,67',
            MonetaryNumberHelper::formatValue($user, $number)
        );
    }

    /** @test */
    public function it_returns_the_amount_with_the_currency_symbol()
    {
        $currency = 'EUR';

        $this->assertEquals('€500.00', MonetaryNumberHelper::formatCurrency(50000, $currency));
        $this->assertEquals('€5,038.29', MonetaryNumberHelper::formatCurrency(503829, $currency));
        $this->assertEquals('500.00', MonetaryNumberHelper::getValue(50000, $currency));
        $this->assertEquals('5038.29', MonetaryNumberHelper::getValue(503829, $currency));
        $this->assertEquals(500, MonetaryNumberHelper::exchangeValue(50000, $currency));
        $this->assertEquals(5038.29, MonetaryNumberHelper::exchangeValue(503829, $currency));
    }

    /** @test */
    public function it_returns_the_amount_with_the_currency_symbol_in_the_right_locale()
    {
        App::setLocale('fr');

        $currency = 'EUR';

        $this->assertEquals('500,00 €', MonetaryNumberHelper::formatCurrency(50000, $currency));
        $this->assertEquals('5 038,29 €', MonetaryNumberHelper::formatCurrency(503829, $currency));
        $this->assertEquals('500,00', MonetaryNumberHelper::getValue(50000, $currency));
        $this->assertEquals('5038,29', MonetaryNumberHelper::getValue(503829, $currency));
        $this->assertEquals(500, MonetaryNumberHelper::exchangeValue(50000, $currency));
        $this->assertEquals(5038.29, MonetaryNumberHelper::exchangeValue(503829, $currency));
    }

    /** @test */
    public function it_returns_the_amount_with_the_currency_symbol_with_the_right_punctuation()
    {
        $currency = 'JPY'; // minorUnit value is zero "0"

        $this->assertEquals('¥500', MonetaryNumberHelper::formatCurrency(500, $currency));
        $this->assertEquals('¥5,038', MonetaryNumberHelper::formatCurrency(5038, $currency));
        $this->assertEquals('500', MonetaryNumberHelper::getValue(500, $currency));
        $this->assertEquals('5038', MonetaryNumberHelper::getValue(5038, $currency));
        $this->assertEquals(500, MonetaryNumberHelper::exchangeValue(500, $currency));
        $this->assertEquals(5038, MonetaryNumberHelper::exchangeValue(5038, $currency));
    }

    /** @test */
    public function it_formats_the_currency_with_the_right_locale()
    {
        $currency = 'GBP';

        $this->assertEquals('£75.00', MonetaryNumberHelper::formatCurrency(7500, $currency));
        $this->assertEquals('£2,734.12', MonetaryNumberHelper::formatCurrency(273412, $currency));
        $this->assertEquals('75.00', MonetaryNumberHelper::getValue(7500, $currency));
        $this->assertEquals('2734.12', MonetaryNumberHelper::getValue(273412, $currency));
        $this->assertEquals(75, MonetaryNumberHelper::exchangeValue(7500, $currency));
        $this->assertEquals(2734.12, MonetaryNumberHelper::exchangeValue(273412, $currency));
    }

    /** @test */
    public function it_returns_the_amount_without_the_currency_symbol_if_not_provided()
    {
        $this->assertEquals('5', MonetaryNumberHelper::formatCurrency(500));
        $this->assertEquals('50', MonetaryNumberHelper::formatCurrency(5000));
    }

    /** @test */
    public function it_covers_brazilian_currency()
    {
        $currency = 'BRL';

        $this->assertEquals('R$12,345.67', MonetaryNumberHelper::formatCurrency(1234567, $currency));
        $this->assertEquals('12345.67', MonetaryNumberHelper::getValue(1234567, $currency));
        $this->assertEquals(12345.67, MonetaryNumberHelper::exchangeValue(1234567, $currency));
    }

    /** @test */
    public function it_parse_an_input_value()
    {
        $currency = 'EUR';

        $this->assertEquals(50000, MonetaryNumberHelper::parseInput('500.00', $currency));
        $this->assertEquals(503829, MonetaryNumberHelper::parseInput('5038.29', $currency));
    }
}
