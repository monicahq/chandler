<?php

namespace App\Http\Controllers\Vault\Settings;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vault\Settings\ViewHelpers\VaultSettingsIndexViewHelper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Models\Vault;
use App\Services\Vault\ManageLabels\CreateLabel;
use App\Services\Vault\ManageLabels\DestroyLabel;
use App\Services\Vault\ManageLabels\UpdateLabel;

class VaultSettingsLabelController extends Controller
{
    public function store(Request $request, int $vaultId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'bg_color' => $request->input('bg_color'),
            'text_color' => $request->input('text_color'),
        ];

        $label = (new CreateLabel)->execute($data);
        $vault = Vault::findOrFail($vaultId);

        return response()->json([
            'data' => VaultSettingsIndexViewHelper::dtoLabel($vault, $label),
        ], 201);
    }

    public function update(Request $request, int $vaultId, int $labelId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'label_id' => $labelId,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'bg_color' => $request->input('bg_color'),
            'text_color' => $request->input('text_color'),
        ];

        $label = (new UpdateLabel)->execute($data);
        $vault = Vault::findOrFail($vaultId);

        return response()->json([
            'data' => VaultSettingsIndexViewHelper::dtoLabel($vault, $label),
        ], 200);
    }

    public function destroy(Request $request, int $vaultId, int $labelId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'label_id' => $labelId,
        ];

        (new DestroyLabel)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
