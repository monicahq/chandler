<?php

namespace App\Vault\ManageCompanies\Web\Controllers;

use App\Vault\ManageCompanies\Web\ViewHelpers\CompanyViewHelper;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use App\Models\Vault;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Vault\ManageVault\Services\CreateVault;
use App\Vault\ManageVault\Services\DestroyVault;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Vault\ManageVault\Web\ViewHelpers\VaultCreateViewHelper;

class ContactCompanyController extends Controller
{
    public function index(Request $request, int $vaultId): JsonResponse
    {
        $vault = Vault::findOrFail($vaultId);
        $collection = CompanyViewHelper::data($vault);

        return response()->json([
            'data' => $collection,
        ], 200);
    }
}
