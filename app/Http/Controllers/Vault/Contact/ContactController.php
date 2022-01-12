<?php

namespace App\Http\Controllers\Vault\Contact;

use Inertia\Inertia;
use App\Models\Vault;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Vault\Contact\ViewHelpers\ContactIndexViewHelper;
use App\Http\Controllers\Vault\Contact\ViewHelpers\ContactCreateViewHelper;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = Contact::where('vault_id', $request->route()->parameter('vault'))
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return Inertia::render('Vault/Contact/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => ContactIndexViewHelper::data($contacts, Auth::user()),
        ]);
    }

    public function create(Request $request, int $vaultId)
    {
        $vault = Vault::findOrFail($vaultId);

        return Inertia::render('Vault/Contact/Create', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => ContactCreateViewHelper::data($vault),
        ]);
    }
}
