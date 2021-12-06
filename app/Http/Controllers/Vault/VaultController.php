<?php

namespace App\Http\Controllers\Vault;

use App\Features\Vault\ManageVault\Services\CreateVault;
use App\Features\Vault\ManageVault\ViewHelpers\VaultCreateViewHelper;
use Inertia\Inertia;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Features\Vault\ManageVault\ViewHelpers\VaultIndexViewHelper;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'data' => VaultCreateViewHelper::data(),
        ]);
    }

    /**
     * Store the vault.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'type' => Vault::TYPE_PERSONAL,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ];

        (new CreateVault)->execute($data);

        return response()->json([
            'data' => route('vault.index'),
        ], 201);
    }
}
