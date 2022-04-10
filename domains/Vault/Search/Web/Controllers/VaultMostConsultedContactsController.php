<?php

namespace App\Vault\Search\Web\Controllers;

use App\Models\Vault;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vault\Search\Web\ViewHelpers\VaultMostConsultedViewHelper;
use Illuminate\Support\Facades\Auth;

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
