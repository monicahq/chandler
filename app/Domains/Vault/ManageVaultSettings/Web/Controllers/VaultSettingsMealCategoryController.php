<?php

namespace App\Domains\Vault\ManageVaultSettings\Web\Controllers;

use App\Domains\Vault\ManageKitchen\Services\CreateMealCategory;
use App\Domains\Vault\ManageKitchen\Services\DestroyMealCategory;
use App\Domains\Vault\ManageKitchen\Services\UpdateMealCategory;
use App\Domains\Vault\ManageVaultSettings\Web\ViewHelpers\VaultSettingsIndexViewHelper;
use App\Http\Controllers\Controller;
use App\Models\Vault;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VaultSettingsMealCategoryController extends Controller
{
    public function store(Request $request, Vault $vault): JsonResponse
    {
        $mealCategory = (new CreateMealCategory())->execute([
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vault->id,
            'label' => $request->input('label'),
        ]);

        return response()->json([
            'data' => VaultSettingsIndexViewHelper::dtoMealCategory($mealCategory),
        ], 201);
    }

    public function update(Request $request, Vault $vault, int $mealCategoryId): JsonResponse
    {
        $mealCategory = (new UpdateMealCategory())->execute([
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vault->id,
            'meal_category_id' => $mealCategoryId,
            'label' => $request->input('label'),
        ]);

        return response()->json([
            'data' => VaultSettingsIndexViewHelper::dtoMealCategory($mealCategory),
        ], 200);
    }

    public function destroy(Request $request, Vault $vault, int $mealCategoryId): JsonResponse
    {
        (new DestroyMealCategory())->execute([
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vault->id,
            'meal_category_id' => $mealCategoryId,
        ]);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
