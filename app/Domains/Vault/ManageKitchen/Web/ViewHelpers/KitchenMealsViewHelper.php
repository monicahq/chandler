<?php

namespace App\Domains\Vault\ManageKitchen\Web\ViewHelpers;

use App\Models\Meal;
use App\Models\MealCategory;
use App\Models\Vault;

class KitchenMealsViewHelper
{
    public static function data(Vault $vault): array
    {
        $meals = $vault->meals()
            ->with('mealCategory')
            ->get()
            ->map(fn (Meal $meal) => self::dto($meal));

        $mealCategories = $vault->mealCategories()
            ->withCount('meals')
            ->get()
            ->map(fn (MealCategory $mealCategory) => [
                'id' => $mealCategory->id,
                'label' => $mealCategory->label,
                'count' => $mealCategory->meals_count,
            ]);

        return [
            'meals' => $meals,
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
                'show' => route('vault.kitchen.meals.show', [
                    'vault' => $meal->vault_id,
                    'meal' => $meal->id,
                ]),
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
