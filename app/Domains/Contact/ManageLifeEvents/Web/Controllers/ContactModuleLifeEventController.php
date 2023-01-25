<?php

namespace App\Domains\Contact\ManageLifeEvents\Web\Controllers;

use App\Domains\Contact\ManageLifeEvents\Services\CreateLifeEvent;
use App\Domains\Contact\ManageLifeEvents\Services\CreateTimelineEvent;
use App\Domains\Contact\ManageLoans\Services\DestroyLoan;
use App\Domains\Contact\ManageLoans\Services\UpdateLoan;
use App\Domains\Contact\ManageLoans\Web\ViewHelpers\ModuleLoanViewHelper;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactModuleLifeEventController extends Controller
{
    public function store(Request $request, int $vaultId, int $contactId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'label' => $request->input('label'),
            'started_at' => $request->input('date'),
        ];

        $timelineEvent = (new CreateTimelineEvent())->execute($data);

        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'label' => $request->input('label'),

        ];

        $timelineEvent = (new CreateLifeEvent())->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLoanViewHelper::dtoLoan($loan, $contact, Auth::user()),
        ], 201);
    }

    public function update(Request $request, int $vaultId, int $contactId, int $loanId)
    {
        $loaners = collect($request->input('loaners'))->pluck('id');
        $loanees = collect($request->input('loanees'))->pluck('id');

        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'loan_id' => $loanId,
            'currency_id' => $request->input('currency_id'),
            'type' => $request->input('type'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'loaner_ids' => $loaners,
            'loanee_ids' => $loanees,
            'amount_lent' => $request->input('amount_lent') ? $request->input('amount_lent') * 100 : null,
            'loaned_at' => $request->input('loaned_at'),
        ];

        $loan = (new UpdateLoan())->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLoanViewHelper::dtoLoan($loan, $contact, Auth::user()),
        ], 200);
    }

    public function destroy(Request $request, int $vaultId, int $contactId, int $loanId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'loan_id' => $loanId,
        ];

        (new DestroyLoan())->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
