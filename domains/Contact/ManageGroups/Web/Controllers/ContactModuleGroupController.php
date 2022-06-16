<?php

namespace App\Contact\ManageGroups\Web\Controllers;

use App\Contact\ManageGoals\Services\CreateGoal;
use App\Contact\ManageGoals\Services\DestroyGoal;
use App\Contact\ManageGoals\Services\UpdateGoal;
use App\Contact\ManageGoals\Web\ViewHelpers\ModuleGoalsViewHelper;
use App\Contact\ManageGroups\Services\AddContactToGroup;
use App\Contact\ManageGroups\Services\CreateGroup;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactModuleGroupController extends Controller
{
    public function store(Request $request, int $vaultId, int $contactId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'group_type_id' => $request->input('group_type_id'),
            'name' => $request->input('name'),
        ];

        $group = (new CreateGroup)->execute($data);

        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'group_id' => $group->id,
        ];

        $group = (new AssignLabel)->execute($data);

        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLabelViewHelper::dtoLabel($group, $contact, true),
        ], 201);
    }

    public function update(Request $request, int $vaultId, int $contactId, int $groupId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'group_id' => $groupId,
        ];

        $group = (new AddContactToGroup)->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLabelViewHelper::dtoLabel($group, $contact, true),
        ], 200);
    }

    public function destroy(Request $request, int $vaultId, int $contactId, int $groupId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'group_id' => $groupId,
        ];

        $group = (new RemoveLabel)->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLabelViewHelper::dtoLabel($group, $contact, false),
        ], 200);
    }
}
