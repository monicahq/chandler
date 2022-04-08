<?php

namespace App\Settings\ManageModules\Web\Controllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Settings\ManageModules\Web\ViewHelpers\PersonalizeModuleIndexViewHelper;
use Illuminate\Support\Facades\Auth;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;

class PersonalizeModulesController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Personalize/Modules/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeModuleIndexViewHelper::data(Auth::user()->account),
        ]);
    }
}
