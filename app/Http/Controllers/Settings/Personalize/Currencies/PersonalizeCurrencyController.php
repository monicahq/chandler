<?php

namespace App\Http\Controllers\Settings\Personalize\Currencies;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Personalize\Currencies\ViewHelpers\PersonalizeCurrencyIndexViewHelper;
use Illuminate\Support\Facades\Auth;
use App\Services\Account\ManageGenders\CreateGender;
use App\Services\Account\ManageGenders\UpdateGender;
use App\Services\Account\ManageGenders\DestroyGender;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Personalize\Genders\ViewHelpers\PersonalizeGenderIndexViewHelper;

class PersonalizeCurrencyController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Personalize/Currencies/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeCurrencyIndexViewHelper::data(Auth::user()->account),
        ]);
    }

    public function update(Request $request, int $currencyId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'gender_id' => $currencyId,
            'name' => $request->input('name'),
        ];

        $gender = (new UpdateGender)->execute($data);

        return response()->json([
            'data' => PersonalizeGenderIndexViewHelper::dtoGender($gender),
        ], 200);
    }
}
