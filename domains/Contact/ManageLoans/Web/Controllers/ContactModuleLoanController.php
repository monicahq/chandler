<?php

namespace App\Contact\ManageLoans\Web\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Contact\ManageLoans\Services\CreateLoan;
use App\Services\Contact\AssignLabel\AssignLabel;
use App\Services\Contact\AssignLabel\RemoveLabel;
use App\Contact\ManageLoans\Web\ViewHelpers\ModuleLoanViewHelper;
use App\Http\Controllers\Vault\Contact\Modules\Label\ViewHelpers\ModuleLabelViewHelper;

class ContactModuleLoanController extends Controller
{
    public function store(Request $request, int $vaultId, int $contactId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'type' => $request->input('type'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'loaner_ids' => $request->input('loaners'),
            'loanee_ids' => $request->input('loanees'),
            'amount_lent' => $request->input('amount_lent'),
            'loaned_at' => $request->input('loaned_at'),
        ];

        $loan = (new CreateLoan)->execute($data);

        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLoanViewHelper::dtoLoan($loan, $contact, Auth::user()),
        ], 201);
    }

    public function update(Request $request, int $vaultId, int $contactId, int $labelId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'label_id' => $labelId,
        ];

        $label = (new AssignLabel)->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLabelViewHelper::dtoLabel($label, $contact, true),
        ], 200);
    }

    public function destroy(Request $request, int $vaultId, int $contactId, int $labelId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'label_id' => $labelId,
        ];

        $label = (new RemoveLabel)->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLabelViewHelper::dtoLabel($label, $contact, false),
        ], 200);
    }
}
