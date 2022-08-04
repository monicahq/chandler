<?php

namespace App\Contact\ManageGoals\Web\Controllers;

use App\Contact\ManageGoals\Web\ViewHelpers\GoalShowViewHelper;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Goal;
use App\Models\Vault;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContactGoalController extends Controller
{
    public function show(Request $request, int $vaultId, int $contactId, int $goalId)
    {
        $vault = Vault::findOrFail($vaultId);
        $contact = Contact::findOrFail($contactId);
        $goal = Goal::where('contact_id', $contact->id)->findOrFail($goalId);

        return Inertia::render('Vault/Contact/Goals/Show', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => GoalShowViewHelper::data($contact, Auth::user(), $goal, Carbon::now()->year),
        ]);
    }
}
