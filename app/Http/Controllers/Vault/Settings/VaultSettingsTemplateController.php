<?php

namespace App\Http\Controllers\Vault\Settings;

use Inertia\Inertia;
use App\Models\Vault;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Vault\Settings\ViewHelpers\VaultSettingsIndexViewHelper;
use App\Services\Vault\ManageVault\UpdateVault;
use App\Services\Vault\ManageVault\UpdateVaultDefaultTemplate;
use Illuminate\Support\Facades\Auth;

class VaultSettingsTemplateController extends Controller
{
    public function update(Request $request, int $vaultId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'template_id' => $request->input('template_id'),
        ];

        (new UpdateVaultDefaultTemplate)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
