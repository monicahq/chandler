<?php

namespace App\Contact\ManageRelationships\Web\Controllers;

use Inertia\Inertia;
use App\Models\Vault;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Helpers\PaginatorHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Contact\ManageNotes\Web\ViewHelpers\NotesIndexViewHelper;
use App\Contact\ManageRelationships\Web\ViewHelpers\ContactRelationshipsCreateViewHelper;

class ContactRelationshipsController extends Controller
{
    public function create(Request $request, int $vaultId, int $contactId)
    {
        $vault = Vault::findOrFail($vaultId);
        $contact = Contact::findOrFail($contactId);

        return Inertia::render('Vault/Contact/Relationships/Create', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => ContactRelationshipsCreateViewHelper::data($vault, $contact, Auth::user()),
        ]);
    }
}
