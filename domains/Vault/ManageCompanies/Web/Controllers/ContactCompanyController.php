<?php

namespace App\Vault\ManageCompanies\Web\Controllers;

use App\Contact\ManageJobInformation\Web\ViewHelpers\ModuleCompanyViewHelper;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Vault\ManageCompanies\Web\ViewHelpers\CompanyViewHelper;

class ContactCompanyController extends Controller
{
    public function index(Request $request, int $vaultId, int $contactId): JsonResponse
    {
        $vault = Vault::findOrFail($vaultId);
        $contact = Contact::findOrFail($contactId);
        $collection = ModuleCompanyViewHelper::list($vault, $contact);

        return response()->json([
            'data' => $collection,
        ], 200);
    }
}
