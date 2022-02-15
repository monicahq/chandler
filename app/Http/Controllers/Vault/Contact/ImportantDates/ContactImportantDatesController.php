<?php

namespace App\Http\Controllers\Vault\Contact\ImportantDates;

use Inertia\Inertia;
use App\Models\Vault;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Helpers\PaginatorHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vault\Contact\ImportantDates\ViewHelpers\ContactImportantDatesViewHelper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Vault\Contact\Notes\ViewHelpers\NotesIndexViewHelper;

class ContactImportantDatesController extends Controller
{
    public function index(Request $request, int $vaultId, int $contactId)
    {
        $vault = Vault::findOrFail($vaultId);
        $contact = Contact::findOrFail($contactId);

        return Inertia::render('Vault/Contact/ImportantDates/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => ContactImportantDatesViewHelper::data($contact, Auth::user()),
        ]);
    }
}
