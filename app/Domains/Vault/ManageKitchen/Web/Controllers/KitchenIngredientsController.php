<?php

namespace App\Domains\Vault\ManageKitchen\Web\Controllers;

use App\Domains\Vault\ManageKitchen\Services\CreateIngredient;
use App\Domains\Vault\ManageKitchen\Services\DestroyIngredient;
use App\Domains\Vault\ManageKitchen\Services\UpdateIngredient;
use App\Domains\Vault\ManageKitchen\Web\ViewHelpers\KitchenIngredientsViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Vault;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class KitchenIngredientsController extends Controller
{
    public function index(Request $request, Vault $vault)
    {
        return Inertia::render('Vault/Kitchen/Ingredients/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => KitchenIngredientsViewHelper::data($vault),
        ]);
    }

    public function store(Request $request, Vault $vault): JsonResponse
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vault->id,
            'label' => $request->input('label'),
        ];

        $ingredient = (new CreateIngredient())->execute($data);

        return response()->json([
            'data' => KitchenIngredientsViewHelper::dto($ingredient),
        ], 201);
    }

    public function update(Request $request, Vault $vault, Ingredient $ingredient): JsonResponse
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vault->id,
            'ingredient_id' => $ingredient->id,
            'label' => $request->input('label'),
        ];

        $ingredient = (new UpdateIngredient())->execute($data);

        return response()->json([
            'data' => KitchenIngredientsViewHelper::dto($ingredient),
        ], 200);
    }

    public function destroy(Request $request, Vault $vault, Ingredient $ingredient): JsonResponse
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vault->id,
            'ingredient_id' => $ingredient->id,
        ];

        (new DestroyIngredient())->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
