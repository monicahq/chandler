<?php

namespace App\Domains\Contact\ManageContact\Web\Controllers;

use App\Domains\Contact\Dav\Services\ExportVCard;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactVCardController extends Controller
{
    public function download(Request $request, int $vault_id, int $contact_id)
    {
        $carddata = $this->exportVCard($request, $vault_id, $contact_id);

        $contact = Contact::findOrFail($contact_id);

        return response($carddata)
            ->withHeaders([
                'Content-Type' => 'text/vcard',
                'Content-Disposition' => 'attachment; filename="'.$contact->uuid.'.vcf"',
            ]);
    }

    /**
     * Get the new exported version of the object.
     */
    protected function exportVCard(Request $request, int $vault_id, int $contact_id): string
    {
        $vcard = app(ExportVCard::class)
            ->execute([
                'account_id' => $request->user()->account_id,
                'author_id' => $request->user()->id,
                'vault_id' => $vault_id,
                'contact_id' => $contact_id,
            ]);

        return $vcard->serialize();
    }
}