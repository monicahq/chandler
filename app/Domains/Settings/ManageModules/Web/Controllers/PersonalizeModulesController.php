<?php

namespace App\Domains\Settings\ManageModules\Web\Controllers;

use App\Domains\Settings\ManageModules\Services\CreateModule;
use App\Domains\Settings\ManageModules\Web\ViewHelpers\PersonalizeModuleIndexViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PersonalizeModulesController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Personalize/Modules/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeModuleIndexViewHelper::data(Auth::user()->account),
        ]);
    }

    public function store(Request $request)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'name' => $request->input('name'),
            'rows' => $request->input('rows'),
        ];

        $module = (new CreateModule())->execute($data);
    }
}
