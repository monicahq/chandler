<?php

namespace App\Http\Controllers\Settings\Personalize\Genders;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Account\ManageLabels\CreateLabel;
use App\Services\Account\ManageLabels\UpdateLabel;
use App\Services\Account\ManageLabels\DestroyLabel;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Personalize\Labels\ViewHelpers\PersonalizeLabelIndexViewHelper;
use App\Services\Account\ManageGenders\CreateGender;
use App\Services\Account\ManageGenders\DestroyGender;
use App\Services\Account\ManageGenders\UpdateGender;

class PersonalizeGenderController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Personalize/Genders/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeGenderIndexViewHelper::data(Auth::user()->account),
        ]);
    }

    public function store(Request $request)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'name' => $request->input('name'),
        ];

        $gender = (new CreateGender)->execute($data);

        return response()->json([
            'data' => PersonalizeGenderIndexViewHelper::dtoLabel($gender),
        ], 201);
    }

    public function update(Request $request, int $genderId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'label_id' => $genderId,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ];

        $gender = (new UpdateGender)->execute($data);

        return response()->json([
            'data' => PersonalizeGenderIndexViewHelper::dtoLabel($gender),
        ], 200);
    }

    public function destroy(Request $request, int $genderId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'label_id' => $genderId,
        ];

        (new DestroyGender)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
