<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlLocalizedDecimalFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class MonetaryNumberHelper
{
    private static $currencies;

    /**
     * Get the currencies list from Money library.
     */
    protected static function getCurrencies(): ISOCurrencies
    {
        if (static::$currencies === null) {
            static::$currencies = new ISOCurrencies();
        }

        return static::$currencies;
    }

    /**
     * Format a monetary amount, without the currency.
     * The value is formatted using current langage.
     *
     * @param  int  $amount  Amount value in storable format (ex: 100 for 1,00€).
     * @param  string|null  $currency  Currency of amount.
     * @return string Formatted amount for display without currency symbol (ex: '1234.50').
     */
    public static function formatValue(User $user, int $amount, ?string $currency = null, ?int $format = \NumberFormatter::DECIMAL): string
    {
        if ($currency === null) {
            $currency = 'USD';
            $format = \NumberFormatter::DECIMAL;
        }

        $money = new Money($amount, new Currency($currency));

        $numberFormatter = new \NumberFormatter(App::getLocale(), $format);
        static::setNumberFormat($user, $numberFormatter);

        if ($format === \NumberFormatter::CURRENCY) {
            $numberFormatter->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, '');
        }

        $moneyFormatter = new IntlLocalizedDecimalFormatter($numberFormatter, static::getCurrencies());

        return trim($moneyFormatter->format($money), ' ');
    }

    /**
     * Format a monetary amount, with the currency.
     * The value is formatted using current langage.
     *
     * @param  int  $amount  Amount value in storable format (ex: 100 for 1,00€).
     * @param  string|null  $currency  Currency of amount.
     * @return string Formatted amount for display without currency symbol (ex: '1234.50').
     */
    public static function format(User $user, int $amount, ?string $currency = null): string
    {
        $value = static::formatValue($user, $amount, $currency);

        if ($currency === null) {
            return $value;
        }

        // // Other working example using MessageFormatter.
        // $number = static::inputValue($amount, $currency);
        // $base = \MessageFormatter::formatMessage(App::getLocale(), "{0, number}", [$number]);
        // $formatted = \MessageFormatter::formatMessage(App::getLocale(), "{0, number, :: currency/$currency}", [$number]);

        $money = new Money($amount, new Currency($currency));

        // First get the base formatted value, without currency symbol.
        $base = static::formatMoney($money, \NumberFormatter::DECIMAL);

        // Then get the formatted value with currency symbol.
        $formatted = static::formatMoney($money);

        // Finally replace the base formatted value with the formatted value.
        return (string) Str::of($formatted)->replace($base, $value);
    }

    private static function formatMoney(Money $money, $format = \NumberFormatter::CURRENCY): string
    {
        $formatter = new \NumberFormatter(App::getLocale(), $format);
        $moneyFormatter = new IntlMoneyFormatter($formatter, static::getCurrencies());

        return $moneyFormatter->format($money);
    }

    /**
     * Parse a monetary value as storable integer.
     * Currency is used to know the precision of this currency.
     *
     * @param  string  $value  Amount value in input format (ex: 145.00).
     * @return int Amount as storable format (ex: 14500).
     */
    public static function parseInput(string $value, ?string $currency = null): int
    {
        if (! $currency) {
            $currency = 'USD';
        }

        $moneyParser = new DecimalMoneyParser(static::getCurrencies());
        $money = $moneyParser->parse($value, new Currency($currency));

        return (int) $money->getAmount();
    }

    /**
     * Format a monetary value as input value.
     * Input value is the amount to be entered in an input by a user,
     * using ordinary format.
     *
     * @param  int  $amount  Amount value in storable format (ex: 100 for 1,00€).
     * @return string Real value of amount in input format (ex: 1.24).
     */
    public static function inputValue(int $amount, ?string $currency = null): string
    {
        if (! $currency) {
            $currency = 'USD';
        }

        $money = new Money($amount, new Currency($currency));

        $moneyFormatter = new DecimalMoneyFormatter(static::getCurrencies());

        return $moneyFormatter->format($money);
    }

    /**
     * Set the numberFormatter symbols according to set user preferences.
     */
    protected static function setNumberFormat(User $user, \NumberFormatter $numberFormatter)
    {
        switch ($user->number_format) {
            // 1,234.56
            case User::NUMBER_FORMAT_TYPE_COMMA_THOUSANDS_DOT_DECIMAL:
                $numberFormatter->setSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, '.');
                $numberFormatter->setSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL, ',');
                break;

                // 1 234,56
            case User::NUMBER_FORMAT_TYPE_SPACE_THOUSANDS_COMMA_DECIMAL:
                $numberFormatter->setSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, ',');
                $numberFormatter->setSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL, ' ');
                break;

                // 1.234,56
            case User::NUMBER_FORMAT_TYPE_DOT_THOUSANDS_COMMA_DECIMAL:
                $numberFormatter->setSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, ',');
                $numberFormatter->setSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '.');
                break;

                // 1234.56
            case User::NUMBER_FORMAT_TYPE_NO_SPACE_DOT_DECIMAL:
                $numberFormatter->setSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, '.');
                $numberFormatter->setSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '');
                break;
        }
    }
}
