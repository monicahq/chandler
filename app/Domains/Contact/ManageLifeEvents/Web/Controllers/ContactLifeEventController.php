<?php

namespace App\Domains\Contact\ManageLifeEvents\Web\Controllers;

use App\Domains\Contact\ManageContactFeed\Web\ViewHelpers\ModuleFeedViewHelper;
use App\Domains\Contact\ManageLifeEvents\Web\ViewHelpers\ModuleLifeEventViewHelper;
use App\Helpers\PaginatorHelper;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactFeedItem;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactLifeEventController extends Controller
{
    public function show(Request $request, int $vaultId, int $contactId)
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
            'data' => ModuleLifeEventViewHelper::timelineEvents($lifeEvents, Auth::user()),
            'paginator' => PaginatorHelper::getData($lifeEvents),
        ], 200);
    }
}
