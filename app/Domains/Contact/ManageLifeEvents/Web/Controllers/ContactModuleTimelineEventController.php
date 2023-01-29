<?php

namespace App\Domains\Contact\ManageLifeEvents\Web\Controllers;

use App\Domains\Contact\ManageLifeEvents\Services\CreateLifeEvent;
use App\Domains\Contact\ManageLifeEvents\Services\CreateTimelineEvent;
use App\Domains\Contact\ManageLifeEvents\Web\ViewHelpers\ModuleLifeEventViewHelper;
use App\Domains\Contact\ManageLoans\Services\DestroyLoan;
use App\Domains\Contact\ManageLoans\Services\UpdateLoan;
use App\Domains\Contact\ManageLoans\Web\ViewHelpers\ModuleLoanViewHelper;
use App\Helpers\PaginatorHelper;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactModuleTimelineEventController extends Controller
{
    public function index(Request $request, int $vaultId, int $contactId)
    {
        // we need to get all the life events for this contact
        // which will give us all the timeline events
        $contact = Contact::where('vault_id', $vaultId)->findOrFail($contactId);

        $lifeEvents = $contact
            ->lifeEvents()
            ->with('timelineEvent')
            ->orderBy('happened_at', 'desc')
            ->paginate(15);

        return response()->json([
            'data' => ModuleLifeEventViewHelper::timelineEvents($lifeEvents, Auth::user(), $contact),
            'paginator' => PaginatorHelper::getData($lifeEvents),
        ], 200);
    }

    public function store(Request $request, int $vaultId, int $contactId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'label' => $request->input('label'),
            'started_at' => $request->input('started_at'),
        ];

        $timelineEvent = (new CreateTimelineEvent())->execute($data);

        // we also need to add the current contact to the list of participants
        // finally, just so we are sure that we don't have the same participant
        // twice in the list, we need to remove duplicates
        $participants = collect($request->input('participants'))
            ->push(['id' => $contactId])
            ->unique('id')
            ->pluck('id')
            ->toArray();

        $carbonDate = Carbon::parse($request->input('started_at'));

        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'label' => $request->input('label'),
            'timeline_event_id' => $timelineEvent->id,
            'life_event_type_id' => $request->input('lifeEventTypeId'),
            'summary' => $request->input('summary'),
            'description' => $request->input('description'),
            'happened_at' => $carbonDate->format('Y-m-d'),
            'costs' => $request->input('costs'),
            'currency_id' => $request->input('currency_id'),
            'paid_by_contact_id' => $request->input('paid_by_contact_id'),
            'duration_in_minutes' => $request->input('duration_in_minutes'),
            'distance_in_km' => $request->input('distance_in_km'),
            'from_place' => $request->input('from_place'),
            'to_place' => $request->input('to_place'),
            'place' => $request->input('place'),
            'participant_ids' => $participants,
        ];

        $lifeEvent = (new CreateLifeEvent())->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLifeEventViewHelper::dtoTimelineEvent($timelineEvent, Auth::user(), $contact),
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