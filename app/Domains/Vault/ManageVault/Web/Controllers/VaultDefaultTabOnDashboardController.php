<?php

namespace App\Domains\Vault\ManageVault\Web\Controllers;

use App\Domains\Contact\ManageLifeEvents\Web\ViewHelpers\ModuleLifeEventViewHelper;
use App\Domains\Vault\ManageVault\Services\CreateVault;
use App\Domains\Vault\ManageVault\Services\DestroyVault;
use App\Domains\Vault\ManageVault\Services\UpdateVaultDashboardDefaultTab;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultCreateViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultShowViewHelper;
use App\Http\Controllers\Controller;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VaultDefaultTabOnDashboardController extends Controller
{
    public function update(Request $request, int $vaultId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'show_activity_tab_on_dashboard' => $request->input('show_activity_tab_on_dashboard'),
        ];

        (new UpdateVaultDashboardDefaultTab())->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
