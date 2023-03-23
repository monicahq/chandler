<?php

namespace App\Domains\Vault\ManageGifts\Web\Controllers;

use App\Domains\Vault\ManageJournals\Web\ViewHelpers\JournalIndexViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Controller;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GiftController extends Controller
{
    public function index(Request $request, int $vaultId)
    {
        $vault = Vault::findOrFail($vaultId);

        return Inertia::render('Vault/Gift/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => JournalIndexViewHelper::data($vault, Auth::user()),
        ]);
    }
}
