<?php

namespace App\Http\Controllers\Vault\Search;

use Inertia\Inertia;
use App\Models\Vault;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vault\Search\ViewHelpers\VaultMostConsultedViewHelper;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Vault\Search\ViewHelpers\VaultSearchIndexViewHelper;
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
