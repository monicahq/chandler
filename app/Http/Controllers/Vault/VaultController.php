<?php

namespace App\Http\Controllers\Vault;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Response;

class VaultController extends Controller
{
    /**
     * Show all the vaults of the user.
     *
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Vault/Index');
    }
}
