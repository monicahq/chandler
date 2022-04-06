<?php

namespace App\Http\Controllers\Vault\Search;

use App\Models\Vault;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vault\Search\ViewHelpers\VaultContactSearchViewHelper;

/**
 * This method is used to search contacts in the modules like "Activity", or
 * "loans".
 */
class VaultContactSearchController extends Controller
{
    public function index(Request $request, int $vaultId)
    {
        $vault = Vault::findOrFail($vaultId);

        return response()->json([
            'data' => VaultContactSearchViewHelper::data($vault, $request->input('searchTerm')),
        ], 200);
    }
}
