<?php

namespace App\Http\Controllers\Settings\Personalize\Currencies\ViewHelpers;

use App\Models\Gender;
use App\Models\Account;
use App\Models\Currency;

class PersonalizeCurrencyIndexViewHelper
{
    public static function data(Account $account): array
    {
        $currencies = $account->currencies()
            ->orderBy('code', 'asc')
            ->get();

        $collection = collect();
        foreach ($currencies as $currency) {
            $collection->push(self::dtoCurrency($currency));
        }

        return [
            'currencies' => $collection,
            'url' => [
                'settings' => route('settings.index'),
                'personalize' => route('settings.personalize.index'),
            ],
        ];
    }

    public static function dtoCurrency(Currency $currency): array
    {
        return [
            'id' => $currency->id,
            'code' => $currency->code,
            'name' => trans('currencies.'.$currency->code),
            'active' => $currency->pivot->active,
            'url' => [
                'update' => route('settings.personalize.currency.update', [
                    'currency' => $currency->id,
                ]),
            ],
        ];
    }
}
