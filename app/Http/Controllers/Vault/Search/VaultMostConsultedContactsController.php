<?php

namespace App\Http\Controllers\Vault\Search;

use App\Models\Vault;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Vault\Search\ViewHelpers\VaultMostConsultedViewHelper;

class VaultMostConsultedContactsController extends Controller
{
    public function index(Request $request, int $vaultId)
    {
        $vault = Vault::findOrFail($vaultId);

        return response()->json([
            'data' => VaultMostConsultedViewHelper::data($vault, Auth::user()),
        ], 200);
    }
}
