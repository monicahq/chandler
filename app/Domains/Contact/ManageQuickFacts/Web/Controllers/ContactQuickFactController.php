<?php

namespace App\Domains\Contact\ManageQuickFacts\Web\Controllers;

use App\Domains\Contact\ManageGroups\Services\RemoveContactFromGroup;
use App\Domains\Contact\ManageGroups\Web\ViewHelpers\ModuleGroupsViewHelper;
use App\Domains\Contact\ManageQuickFacts\Services\CreateQuickFact;
use App\Domains\Contact\ManageQuickFacts\Web\ViewHelpers\ContactModuleQuickFactViewHelper;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactQuickFactController extends Controller
{
    public function show(Request $request, int $vaultId, int $contactId, int $templateId): JsonResponse
    {
        $contact = Contact::find($contactId);
        $template = $contact->vault->quickFactsTemplateEntries()->findOrFail($templateId);

        return response()->json([
            'data' => ContactModuleQuickFactViewHelper::data($contact, $template),
        ], 200);
    }

    public function store(Request $request, int $vaultId, int $contactId, int $templateId): JsonResponse
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'vault_quick_facts_template_id' => $templateId,
            'content' => $request->input('content'),
        ];

        $quickFact = (new CreateQuickFact())->execute($data);

        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ContactModuleQuickFactViewHelper::dto($contact, $quickFact),
        ], 201);
    }

    public function destroy(Request $request, int $vaultId, int $contactId, int $groupId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'group_id' => $groupId,
        ];

        $group = (new RemoveContactFromGroup())->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleGroupsViewHelper::dto($contact, $group, false),
        ], 200);
    }
}
