<?php

namespace App\Domains\Vault\ManageKitchen\Web\Controllers;

use App\Domains\Vault\ManageKitchen\Web\ViewHelpers\KitchenMealsViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Controller;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class KitchenController extends Controller
{
    public function index(Request $request, Vault $vault): Response
    {
        return Inertia::render('Vault/Kitchen/Meals/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => KitchenMealsViewHelper::data($vault, Auth::user()),
        ]);
    }
}
