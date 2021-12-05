<?php

namespace App\Features\Vault\ManageVault\ViewHelpers;

use Illuminate\Support\Facades\Auth;

class VaultIndexViewHelper
{
    /**
     * Get all the data needed for the general layout page.
     *
     * @return array
     */
    public static function loggedUserInformation(): array
    {
        return [
            'name' => Auth::user()->name,
        ];
    }
}
