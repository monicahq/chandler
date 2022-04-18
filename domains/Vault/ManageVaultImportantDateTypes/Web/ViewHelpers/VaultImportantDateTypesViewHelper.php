<?php

namespace App\Vault\ManageVaultImportantDateTypes\Web\ViewHelpers;

use App\Models\User;
use App\Models\Label;
use App\Models\Vault;
use App\Helpers\VaultHelper;
use App\Models\ContactImportantDateType;

class VaultImportantDateTypesViewHelper
{
    public static function data(Vault $vault): array
    {
        $types = $vault->contactImportantDateTypes;
        $typesCollection = $types->map(function ($type) {
            return self::dto($vault, $label);
        });

        return [
            'templates' => $typesCollection,
        ];
    }

    public static function dto(ContactImportantDateType $type, Vault $vault): array
    {
        return [
            'id' => $type->id,
            'label' => $type->label,
            'can_be_deleted' => $type->can_be_deleted,
            'url' => [
                'update' => route('vault.settings.user.update', [
                    'vault' => $vault->id,
                    'user' => $user->id,
                ]),
                'destroy' => route('vault.settings.user.destroy', [
                    'vault' => $vault->id,
                    'user' => $user->id,
                ]),
            ],
        ];
    }
}
