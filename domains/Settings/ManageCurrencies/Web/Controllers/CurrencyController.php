<?php

namespace App\Settings\ManageCurrencies\Web\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Settings\ManageCurrencies\Services\ToggleCurrency;
use App\Settings\ManageCurrencies\Services\EnableAllCurrencies;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Settings\ManageCurrencies\Services\DisableAllCurrencies;
use App\Settings\ManageCurrencies\Web\ViewHelpers\CurrencyIndexViewHelper;
use App\Settings\ManageCurrencies\Web\ViewHelpers\PersonalizeCurrencyIndexViewHelper;

class CurrencyController extends Controller
{
    public function index()
    {
        $currenciesCollection = CurrencyIndexViewHelper::data(Auth::user()->account);

        return response()->json([
            'data' => $currenciesCollection,
        ], 201);
    }
}
