<?php

namespace App\Domains\Vault\ManageVaultSettings\Web\Controllers;

use App\Domains\Vault\ManageVault\Services\UpdateVaultTabVisibility;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VaultSettingsTabVisibilityController extends Controller
{
    public function update(Request $request, int $vaultId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'show_group_tab' => $request->input('show_group_tab'),
            'show_tasks_tab' => $request->input('show_tasks_tab'),
            'show_files_tab' => $request->input('show_files_tab'),
            'show_journal_tab' => $request->input('show_journal_tab'),
        ];

        (new UpdateVaultTabVisibility())->execute($data);
    }
}
