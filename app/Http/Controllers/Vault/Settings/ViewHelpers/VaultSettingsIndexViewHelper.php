<?php

namespace App\Http\Controllers\Vault\Settings\ViewHelpers;

use App\Models\Vault;

// TODO
class VaultSettingsIndexViewHelper
{
    public static function data(Vault $vault): array
    {
        return [
            'url' => [
                'destroy' => route('vault.settings.destroy', [
                    'vault' => $vault->id,
                ]),
            ],
        ];
    }
}
