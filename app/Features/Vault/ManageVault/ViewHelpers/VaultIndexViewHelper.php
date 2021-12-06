<?php

namespace App\Features\Vault\ManageVault\ViewHelpers;

use App\Models\Vault;
use App\Models\Account;
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
            'url' => [
                'logout' => route('logout'),
            ],
        ];
    }

    /**
     * Get all the data needed for the general layout page.
     *
     * @param  Account  $account
     * @return array
     */
    public static function data(Account $account): array
    {
        $vaults = Vault::where('account_id', $account->id)->get();
        $vaultCollection = collect();
        foreach ($vaults as $vault) {
            $vaultCollection->push([
                'id' => $vault->id,
                'name' => $vault->name,
                'description' => $vault->description,
                'url' => [
                    'show' => route('vault.show', [
                        'vault' => $vault,
                    ]),
                ],
            ]);
        }

        return [
            'vaults' => $vaultCollection,
            'url' => [
                'vault' => [
                    'new' => route('vault.new'),
                ],
            ],
        ];
    }
}
