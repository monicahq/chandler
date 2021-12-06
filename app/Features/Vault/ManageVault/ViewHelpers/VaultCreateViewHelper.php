<?php

namespace App\Features\Vault\ManageVault\ViewHelpers;

use Illuminate\Support\Facades\Auth;

class VaultCreateViewHelper
{
    /**
     * Get all the data needed for the page.
     *
     * @return array
     */
    public static function data(): array
    {
        return [
            'url' => [
                'store' => route('vault.store'),
                'back' => route('vault.index'),
            ],
        ];
    }
}
