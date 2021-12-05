<?php

namespace App\Features\Vault\ManageVault\ViewHelpers;

use App\Helpers\ImageHelper;
use App\Models\Company\Company;
use App\Models\Company\JobOpening;
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
