<?php

namespace App\Http\Controllers\Settings\Personalize\Modules;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Personalize\Modules\ViewHelpers\PersonalizeModuleIndexViewHelper;

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
