<?php

namespace App\Http\Controllers\Vault;

use Inertia\Inertia;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Features\Vault\ManageVault\ViewHelpers\VaultIndexViewHelper;

class VaultController extends Controller
{
    /**
     * Show all the vaults of the user.
     *
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Vault/Index', [
            'user' => VaultIndexViewHelper::loggedUserInformation(),
            'data' => VaultIndexViewHelper::data(),
        ]);
    }

    /**
     * Display the create vault page.
     *
     * @return Response
     */
    public function new()
    {
        return Inertia::render('Vault/Create', [
            'user' => VaultIndexViewHelper::loggedUserInformation(),
        ]);
    }
}
