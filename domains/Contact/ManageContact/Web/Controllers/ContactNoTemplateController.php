<?php

namespace App\Contact\ManageContact\Web\Controllers;

use Inertia\Inertia;
use App\Models\Vault;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Contact\ManageContact\Services\CreateContact;
use App\Contact\ManageContact\Services\UpdateContact;
use App\Contact\ManageContact\Services\DestroyContact;
use App\Contact\ManageContact\Web\ViewHelpers\ContactShowBlankViewHelper;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Contact\ManageContact\Web\ViewHelpers\ContactEditViewHelper;
use App\Contact\ManageContact\Web\ViewHelpers\ContactShowViewHelper;
use App\Contact\ManageContact\Web\ViewHelpers\ContactIndexViewHelper;
use App\Contact\ManageContact\Web\ViewHelpers\ContactCreateViewHelper;

class ContactNoTemplateController extends Controller
{
    public function show(Request $request, int $vaultId, int $contactId)
    {
        $vault = Vault::findOrFail($vaultId);
        $contact = Contact::findOrFail($contactId);

        return Inertia::render('Vault/Contact/Blank', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => ContactShowBlankViewHelper::data($contact),
        ]);
    }
}
