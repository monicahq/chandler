<?php

namespace App\Settings\ManageCurrencies\Web\ViewHelpers;

use App\Models\Account;
use Illuminate\Support\Collection;

class CurrencyIndexViewHelper
{
    public static function data(Account $account): Collection
    {
        $currenciesCollection = $account->currencies()
            ->where('active', true)->get()->map(function ($currency) {
                return [
                    'id' => $currency->id,
                    'name' => $currency->code,
                ];
            });

        return $currenciesCollection;
    }
}
