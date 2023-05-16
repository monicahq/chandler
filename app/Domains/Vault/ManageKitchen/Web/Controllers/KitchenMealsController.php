<?php

namespace App\Domains\Vault\ManageKitchen\Web\Controllers;

use App\Domains\Vault\ManageKitchen\Services\CreateMeal;
use App\Domains\Vault\ManageKitchen\Services\DestroyMeal;
use App\Domains\Vault\ManageKitchen\Services\UpdateMeal;
use App\Domains\Vault\ManageKitchen\Web\ViewHelpers\KitchenMealsViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Vault;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class KitchenMealsController extends Controller
{
    public function index(Request $request, Vault $vault): Response
    {
        return Inertia::render('Vault/Kitchen/Meals/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => KitchenMealsViewHelper::data($vault),
        ]);
    }

    public function store(Request $request, Vault $vault): JsonResponse
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vault->id,
            'name' => $request->input('name'),
        ];

        $meal = (new CreateMeal())->execute($data);

        return response()->json([
            'data' => KitchenMealsViewHelper::dto($meal),
        ], 201);
    }

    public function show(Request $request, Vault $vault, Meal $meal): Response
    {
        return Inertia::render('Vault/Kitchen/Meals/Show', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => KitchenMealsViewHelper::data($vault),
        ]);
    }

    public function update(Request $request, Vault $vault, Meal $meal): JsonResponse
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vault->id,
            'meal_id' => $meal->id,
            'name' => $request->input('name'),
        ];

        $meal = (new UpdateMeal())->execute($data);

        return response()->json([
            'data' => KitchenMealsViewHelper::dto($meal),
        ], 200);
    }

    public function destroy(Request $request, Vault $vault, Meal $meal): JsonResponse
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vault->id,
            'meal_id' => $meal->id,
        ];

        (new DestroyMeal())->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
