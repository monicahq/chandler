<?php

namespace App\Domains\Vault\ManageKitchen\Web\ViewHelpers;

use App\Models\MealCategory;
use App\Models\User;
use App\Models\Vault;

class KitchenSettingsViewHelper
{
    public static function data(Vault $vault, User $user): array
    {
        $mealCategories = $vault->mealCategories()
            ->orderBy('position')
            ->get()
            ->map(fn (MealCategory $mealCategory) => self::dto($mealCategory));

        return [
            'meal_categories' => $mealCategories,
            'url' => [
                'store' => route('vault.kitchen.meal_categories.store', [
                    'vault' => $vault->id,
                ]),
            ],
        ];
    }

    public static function dto(MealCategory $mealCategory): array
    {
        return [
            'id' => $mealCategory->id,
            'label' => $mealCategory->label,
            'position' => $mealCategory->position,
            'url' => [
                'update' => route('vault.kitchen.meal_categories.update', [
                    'vault' => $mealCategory->vault_id,
                    'mealCategory' => $mealCategory->id,
                ]),
                'destroy' => route('vault.kitchen.meal_categories.destroy', [
                    'vault' => $vault->id,
                    'mealCategory' => $mealCategory->id,
                ]),
            ],
        ];
    }
}
