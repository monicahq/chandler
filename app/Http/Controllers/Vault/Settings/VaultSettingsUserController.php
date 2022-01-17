<?php

namespace App\Http\Controllers\Vault\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vault\Settings\ViewHelpers\VaultSettingsIndexViewHelper;
use App\Models\Vault;
use Illuminate\Support\Facades\Auth;
use App\Services\Vault\ManageVault\UpdateVaultDefaultTemplate;
use App\Services\Vault\ManageVaultUsers\GrantVaultAccessToUser;
use App\Services\Vault\ManageVaultUsers\RemoveVaultAccess;

class VaultSettingsUserController extends Controller
{
    public function store(Request $request, int $vaultId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'user_id' => $request->input('user_id'),
            'permission' => $request->input('permission'),
        ];

        $user = (new GrantVaultAccessToUser)->execute($data);

        $vault = Vault::findOrFail($vaultId);

        return response()->json([
            'data' => VaultSettingsIndexViewHelper::dtoUser($user, $vault),
        ], 201);
    }

    public function destroy(Request $request, int $vaultId, int $userId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'user_id' => $userId,
        ];

        (new RemoveVaultAccess)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
