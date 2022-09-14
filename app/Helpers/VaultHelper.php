<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Vault;
use Illuminate\Support\Facades\Cache;

class VaultHelper
{
    /**
     * Get the friendly name of a vault permission.
     *
     * @param  int  $permission
     * @return string
     */
    public static function getPermissionFriendlyName(int $permission): string
    {
        switch ($permission) {
            case Vault::PERMISSION_MANAGE:
                $friendlyType = trans('account.vault_permission_manage');
                break;

            case Vault::PERMISSION_EDIT:
                $friendlyType = trans('account.vault_permission_edit');
                break;

            case Vault::PERMISSION_VIEW:
                $friendlyType = trans('account.vault_permission_view');
                break;

            default:
                $friendlyType = '';
                break;
        }

        return $friendlyType;
    }

    /**
     * Get the permission for the given user in the given vault.
     *
     * @param  User  $user
     * @param  Vault  $vault
     * @return int|null
     */
    public static function getPermission(User $user, Vault $vault): ?int
    {
        return Cache::store('array')->remember("Permission:{$user->id}:{$vault->id}", 5, function () use ($user, $vault): ?int {
            $vaults = $user->vaults()
            ->wherePivot('vault_id', $vault->id)
            ->first();

            return $vaults !== null ? $vaults->pivot->permission : null;
        });
    }
}
