<?php

namespace App\Domains\Contact\ManageQuickFacts\Web\Controllers;

use App\Domains\Contact\ManageGroups\Services\AddContactToGroup;
use App\Domains\Contact\ManageGroups\Services\CreateGroup;
use App\Domains\Contact\ManageGroups\Services\RemoveContactFromGroup;
use App\Domains\Contact\ManageGroups\Web\ViewHelpers\ModuleGroupsViewHelper;
use App\Domains\Contact\ManageQuickFacts\Services\CreateQuickFact;
use App\Domains\Contact\ManageQuickFacts\Services\ToggleQuickFactModule;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactQuickFactToggleController extends Controller
{
    public function update(Request $request, int $vaultId, int $contactId): JsonResponse
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
        ];

        (new ToggleQuickFactModule())->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
