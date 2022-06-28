<?php

namespace App\Contact\ManageContact\Web\Controllers;

use App\Contact\ManageContact\Services\CreateContact;
use App\Contact\ManageContact\Services\DestroyContact;
use App\Contact\ManageContact\Services\ToggleArchiveContact;
use App\Contact\ManageContact\Services\UpdateContact;
use App\Contact\ManageContact\Services\UpdateContactView;
use App\Contact\ManageContact\Web\ViewHelpers\ContactCreateViewHelper;
use App\Contact\ManageContact\Web\ViewHelpers\ContactEditViewHelper;
use App\Contact\ManageContact\Web\ViewHelpers\ContactIndexViewHelper;
use App\Contact\ManageContact\Web\ViewHelpers\ContactShowViewHelper;
use App\Helpers\PaginatorHelper;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Vault;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContactArchiveController extends Controller
{
    public function update(Request $request, int $vaultId, int $contactId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
        ];

        (new ToggleArchiveContact())->execute($data);

        return response()->json([
            'data' => route('contact.show', [
                'vault' => $vaultId,
                'contact' => $contactId,
            ]),
        ], 200);
    }
}
