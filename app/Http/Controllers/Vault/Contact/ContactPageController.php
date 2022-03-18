<?php

namespace App\Http\Controllers\Vault\Contact;

use Inertia\Inertia;
use App\Models\Vault;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
use App\Models\TemplatePage;

class ContactPageController extends Controller
{
    public function show(Request $request, int $vaultId, int $contactId, string $slug)
    {
        $vault = Vault::findOrFail($vaultId);
        $contact = Contact::with('gender')
            ->with('pronoun')
            ->with('notes')
            ->with('dates')
            ->with('vault')
            ->findOrFail($contactId);

        if (! $contact->template_id) {
            return redirect()->route('contact.blank', [
                'vault' => $vaultId,
                'contact' => $contactId,
            ]);
        }

        $templatePage = TemplatePage::where('slug', $slug)
            ->where('template_id', $contact->template_id)
            ->firstOrFail();

        return Inertia::render('Vault/Contact/Show', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => ContactShowViewHelper::dataForTemplatePage($contact, Auth::user(), $templatePage),
        ]);
    }
}
