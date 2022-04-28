<?php

namespace App\Vault\ManageCompanies\Web\Controllers;

use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Vault\ManageCompanies\Web\ViewHelpers\CompanyViewHelper;

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
