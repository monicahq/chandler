<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class MonetaryNumberHelper
{
    /**
     * Format the number according to the user preferences.
     */
    public static function formatValue(User $user, int $amount, ?string $currency = null): string
    {
        switch ($user->number_format) {
            // 1,234.56
            case User::NUMBER_FORMAT_TYPE_COMMA_THOUSANDS_DOT_DECIMAL:
                return static::getValue($amount, $currency, 'en');

                // 1 234,56
            case User::NUMBER_FORMAT_TYPE_SPACE_THOUSANDS_COMMA_DECIMAL:
                return static::getValue($amount, $currency, 'fr');

                // 1.234,56
            case User::NUMBER_FORMAT_TYPE_DOT_THOUSANDS_COMMA_DECIMAL:
                return static::getValue($amount, $currency, 'de');

                // 1234.56
            case User::NUMBER_FORMAT_TYPE_NO_SPACE_DOT_DECIMAL:
                return static::exchangeValue($amount, $currency);

            default:
                return '';
        }
    }

    /**
     * Format the amount with the currency symbol according to the user preferences.
     */
    public static function format(User $user, int $amount, ?string $currency = null): string
    {
        switch ($user->number_format) {
            // 1,234.56
            case User::NUMBER_FORMAT_TYPE_COMMA_THOUSANDS_DOT_DECIMAL:
                return static::formatCurrency($amount, $currency, 'en-US');

                // 1 234,56
            case User::NUMBER_FORMAT_TYPE_SPACE_THOUSANDS_COMMA_DECIMAL:
                return static::formatCurrency($amount, $currency, 'fr');

                // 1.234,56
            case User::NUMBER_FORMAT_TYPE_DOT_THOUSANDS_COMMA_DECIMAL:
                return static::formatCurrency($amount, $currency, 'de');

                // 1234.56
            case User::NUMBER_FORMAT_TYPE_NO_SPACE_DOT_DECIMAL:
                return static::formatCurrency($amount, $currency, format: \NumberFormatter::DECIMAL);

            default:
                return '';
        }
    }

    /**
     * Format a monetary amount with currency symbol.
     * The value is formatted using current langage, as per the currency symbol.
     *
     * If the currency parameter is not passed, then the currency specified in
     * the users's settings will be used. If the currency setting is not
     * defined, then the amount will be returned without a currency symbol.
     *
     * @param  int  $amount  Amount value in storable format (ex: 100 for 1,00€).
     * @param  string|null  $currency  Currency of amount.
     * @return string Formatted amount for display with currency symbol (ex '1,235.87 €').
     */
    public static function formatCurrency(int $amount, ?string $currency = null, ?string $locale = null, ?int $format = \NumberFormatter::CURRENCY): string
    {
        if (! $currency) {
            $currency = 'USD';
            $format = \NumberFormatter::DECIMAL;
        }

        $money = new Money($amount, new Currency($currency));
        $numberFormatter = new \NumberFormatter($locale ?? App::getLocale(), $format);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        return $moneyFormatter->format($money);
    }

    /**
     * Format a monetary amount, without the currency.
     * The value is formatted using current langage.
     *
     * @param  int  $amount  Amount value in storable format (ex: 100 for 1,00€).
     * @param  string|null  $currency  Currency of amount.
     * @return string Formatted amount for display without currency symbol (ex: '1234.50').
     */
    public static function getValue(int $amount, ?string $currency = null, ?string $locale = null): string
    {
        if (! $currency) {
            $currency = 'USD';
        }

        $money = new Money($amount, new Currency($currency));
        $numberFormatter = new \NumberFormatter($locale ?? App::getLocale(), \NumberFormatter::DECIMAL);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        return $moneyFormatter->format($money);
    }

    /**
     * Parse a monetary exchange value as storable integer.
     * Currency is used to know the precision of this currency.
     *
     * @param  string  $exchange  Amount value in exchange format (ex: 1.00).
     * @return int Amount as storable format (ex: 14500).
     */
    public static function parseInput(string $exchange, ?string $currency): int
    {
        if (! $currency) {
            $currency = 'USD';
        }

        $moneyParser = new DecimalMoneyParser(new ISOCurrencies());
        $money = $moneyParser->parse($exchange, new Currency($currency));

        return (int) $money->getAmount();
    }

    /**
     * Format a monetary value as exchange value.
     * Exchange value is the amount to be entered in an input by a user,
     * using ordinary format.
     *
     * @param  int  $amount  Amount value in storable format (ex: 100 for 1,00€).
     * @return string Real value of amount in exchange format (ex: 1.24).
     */
    public static function exchangeValue(int $amount, ?string $currency): string
    {
        if (! $currency) {
            $currency = 'USD';
        }

        $money = new Money($amount, new Currency($currency));
        $moneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());

        return $moneyFormatter->format($money);
    }
}
