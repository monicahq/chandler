<?php

namespace App\Domains\Vault\ManageCalendar\Web\Controllers;

use App\Domains\Contact\ManageDocuments\Services\DestroyFile;
use App\Domains\Vault\ManageCalendar\Web\ViewHelpers\VaultCalendarIndexViewHelper;
use App\Domains\Vault\ManageFiles\Web\ViewHelpers\VaultFileIndexViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Helpers\PaginatorHelper;
use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VaultCalendarController extends Controller
{
    public function index(Request $request, int $vaultId)
    {
        $vault = Vault::findOrFail($vaultId);

        return Inertia::render('Vault/Calendar/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => VaultCalendarIndexViewHelper::data($vault),
        ]);
    }
}
