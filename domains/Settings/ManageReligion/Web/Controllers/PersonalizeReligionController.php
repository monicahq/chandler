<?php

namespace App\Settings\ManageReligion\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Settings\ManageReligion\Services\CreateReligion;
use App\Settings\ManageReligion\Services\DestroyReligion;
use App\Settings\ManageReligion\Services\UpdateReligion;
use App\Settings\ManageReligion\Web\ViewHelpers\PersonalizeReligionViewHelper;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PersonalizeReligionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Settings/Personalize/Religions/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeReligionViewHelper::data(Auth::user()->account),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'name' => $request->input('name'),
        ];

        $religion = (new CreateReligion())->execute($data);

        return response()->json([
            'data' => PersonalizeReligionViewHelper::dto($religion),
        ], 201);
    }

    public function update(Request $request, int $religionId): JsonResponse
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'religion_id' => $religionId,
            'name' => $request->input('name'),
        ];

        $religion = (new UpdateReligion())->execute($data);

        return response()->json([
            'data' => PersonalizeReligionViewHelper::dto($religion),
        ], 200);
    }

    public function destroy(Request $request, int $religionId): JsonResponse
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'religion_id' => $religionId,
        ];

        (new DestroyReligion())->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
