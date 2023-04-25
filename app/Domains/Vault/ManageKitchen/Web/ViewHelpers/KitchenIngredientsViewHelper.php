<?php

namespace App\Domains\Vault\ManageKitchen\Web\ViewHelpers;

use App\Models\Ingredient;
use App\Models\Vault;

class KitchenIngredientsViewHelper
{
    public static function data(Vault $vault): array
    {
        $ingredients = $vault->ingredients()
            ->get()
            ->map(fn (Ingredient $ingredient) => self::dto($ingredient));

        return [
            'ingredients' => $ingredients,
            'url' => [
                'store' => route('vault.kitchen.ingredients.store', [
                    'vault' => $vault->id,
                ]),
            ],
        ];
    }

    public static function dto(Ingredient $ingredient): array
    {
        return [
            'id' => $ingredient->id,
            'label' => $ingredient->label,
            'url' => [
                'update' => route('vault.kitchen.ingredients.update', [
                    'vault' => $ingredient->vault_id,
                    'ingredient' => $ingredient->id,
                ]),
                'destroy' => route('vault.kitchen.ingredients.destroy', [
                    'vault' => $ingredient->vault_id,
                    'ingredient' => $ingredient->id,
                ]),
            ],
        ];
    }
}
