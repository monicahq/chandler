<?php

namespace App\Vault\ManageJobInformation\Web\Controllers;

use App\Contact\ManageJobInformation\Services\UpdateJobInformation;
use App\Contact\ManageJobInformation\Web\ViewHelpers\ModuleCompanyViewHelper;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contact;
use App\Vault\ManageCompanies\Services\CreateCompany;
use App\Vault\ManageCompanies\Web\ViewHelpers\CompanyViewHelper;
use Illuminate\Support\Facades\Auth;

class ContactModuleJobInformationController extends Controller
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

    public function store(Request $request, int $vaultId, int $contactId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'name' => $request->input('company_name'),
            'type' => Company::TYPE_COMPANY,
        ];

        $company = (new CreateCompany)->execute($data);

        (new UpdateJobInformation)->execute([
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'company_id' => $company->id,
            'job_position' => $request->input('job_position'),
        ]);

        $contact = Contact::findOrFail($contactId);

        return response()->json([
            'data' => ModuleCompanyViewHelper::dto($company, $contact),
        ], 200);
    }
}
