<?php

namespace App\Http\Controllers\Settings;

use Inertia\Inertia;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Features\Vault\ManageVault\Services\CreateVault;
use App\Features\Vault\ManageVault\ViewHelpers\VaultIndexViewHelper;
use App\Features\Vault\ManageVault\ViewHelpers\VaultCreateViewHelper;

class SettingsController extends Controller
{
    /**
     * Show the settings page.
     *
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Settings/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => VaultIndexViewHelper::data(Auth::user()->account),
        ]);
    }
}
