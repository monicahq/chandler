<?php

namespace App\Domains\Vault\ManageKitchen\Web\ViewHelpers;

use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\MealCategory;
use App\Models\Vault;

class KitchenMealsShowViewHelper
{
    public static function data(Vault $vault, Meal $meal): array
    {
        $mealCategories = $vault->mealCategories()
            ->get()
            ->map(fn (MealCategory $mealCategory) => [
                'id' => $mealCategory->id,
                'label' => $mealCategory->label,
            ]);

        $ingredients = $vault->ingredients()
            ->get()
            ->map(fn (Ingredient $ingredient) => [
                'id' => $ingredient->id,
                'label' => $ingredient->label,
            ]);

        return [
            'meal' => self::dto($meal),
            'ingredients' => $ingredients,
            'meal_categories' => $mealCategories,
            'url' => [
                'ingredients' => route('vault.kitchen.ingredients.index', [
                    'vault' => $vault->id,
                ]),
                'store' => route('vault.kitchen.meals.store', [
                    'vault' => $vault->id,
                ]),
            ],
        ];
    }

    public static function dto(Meal $meal): array
    {
        return [
            'id' => $meal->id,
            'name' => $meal->name,
            'time_to_prepare_in_minutes' => $meal->time_to_prepare_in_minutes,
            'time_to_cook_in_minutes' => $meal->time_to_cook_in_minutes,
            'meal_category' => $meal->mealCategory ? [
                'id' => $meal->mealCategory->id,
                'label' => $meal->mealCategory->label,
            ] : null,
            'url' => [
                'update' => route('vault.kitchen.meals.update', [
                    'vault' => $meal->vault_id,
                    'meal' => $meal->id,
                ]),
                'destroy' => route('vault.kitchen.meals.destroy', [
                    'vault' => $meal->vault_id,
                    'meal' => $meal->id,
                ]),
            ],
        ];
    }
}
