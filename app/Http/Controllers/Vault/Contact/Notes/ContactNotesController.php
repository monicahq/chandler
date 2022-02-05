<?php

namespace App\Http\Controllers\Vault\Contact\Notes;

use App\Helpers\PaginatorHelper;
use Inertia\Inertia;
use App\Models\Vault;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vault\Contact\Notes\ViewHelpers\NotesIndexViewHelper;
use Illuminate\Support\Facades\Auth;
use App\Services\Contact\ManageContact\CreateContact;
use App\Services\Contact\ManageContact\UpdateContact;
use App\Services\Contact\ManageContact\DestroyContact;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Vault\Contact\ViewHelpers\ContactEditViewHelper;
use App\Http\Controllers\Vault\Contact\ViewHelpers\ContactShowViewHelper;
use App\Http\Controllers\Vault\Contact\ViewHelpers\ContactIndexViewHelper;
use App\Http\Controllers\Vault\Contact\ViewHelpers\ContactCreateViewHelper;
use App\Http\Controllers\Vault\Contact\ViewHelpers\ContactShowBlankViewHelper;

class ContactNotesController extends Controller
{
    public function index(Request $request, int $vaultId, int $contactId)
    {
        $vault = Vault::findOrFail($vaultId);
        $contact = Contact::findOrFail($contactId);

        $notes = $contact->notes()->orderBy('created_at', 'desc')->paginate(25);

        return Inertia::render('Vault/Contact/Notes/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => NotesIndexViewHelper::data($contact, $notes),
            'paginator' => PaginatorHelper::getData($notes),
        ]);
    }
}
