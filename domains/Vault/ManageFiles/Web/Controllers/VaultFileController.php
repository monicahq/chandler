<?php

namespace App\Vault\ManageFiles\Web\Controllers;

use App\Helpers\PaginatorHelper;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\File;
use App\Models\Vault;
use App\Vault\ManageFiles\Web\ViewHelpers\VaultFileIndexViewHelper;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VaultFileController extends Controller
{
    public function index(Request $request, int $vaultId)
    {
        $vault = Vault::findOrFail($vaultId);

        $contactIds = Contact::where('vault_id', $vault->id)->select('id')->get()->pluck('id')->toArray();

        $files = File::whereIn('contact_id', $contactIds)
            ->with('contact')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return Inertia::render('Vault/Files/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => VaultFileIndexViewHelper::data($files, Auth::user()),
            'paginator' => PaginatorHelper::getData($files),
        ]);
    }
}
