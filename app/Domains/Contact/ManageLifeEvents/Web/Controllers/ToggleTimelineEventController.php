<?php

namespace App\Domains\Contact\ManageLifeEvents\Web\Controllers;

use App\Domains\Contact\ManageLifeEvents\Services\CreateLifeEvent;
use App\Domains\Contact\ManageLifeEvents\Services\CreateTimelineEvent;
use App\Domains\Contact\ManageLifeEvents\Services\ToggleTimelineEvent;
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

class ToggleTimelineEventController
{
    public function store(Request $request, int $vaultId, int $contactId, int $timelineEventId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'timeline_event_id' => $timelineEventId,
        ];

        (new ToggleTimelineEvent())->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
