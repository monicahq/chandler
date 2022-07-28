<?php

namespace App\Contact\ManagePhotos\Web\Controllers;

use App\Contact\ManagePhotos\Web\ViewHelpers\ContactPhotosIndexViewHelper;
use App\Helpers\PaginatorHelper;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\File;
use App\Models\Vault;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactPhotoController extends Controller
{
    public function index(Request $request, int $vaultId, int $contactId)
    {
        $vault = Vault::findOrFail($vaultId);
        $contact = Contact::findOrFail($contactId);

        $files = File::where('contact_id', $contactId)
            ->where('type', File::TYPE_PHOTO)
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return Inertia::render('Vault/Contact/Photos/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => ContactPhotosIndexViewHelper::data($files, $contact),
            'paginator' => PaginatorHelper::getData($files),
        ]);
    }
}
