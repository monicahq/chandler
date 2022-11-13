<?php

namespace App\Domains\Settings\ManageModules\Web\Controllers;

use App\Domains\Settings\ManageModules\Services\CreateModule;
use App\Domains\Settings\ManageModules\Web\ViewHelpers\PersonalizeModuleCreateViewHelper;
use App\Domains\Settings\ManageModules\Web\ViewHelpers\PersonalizeModuleIndexViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Redirect;

class PersonalizeModulesController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Personalize/Modules/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeModuleIndexViewHelper::data(Auth::user()->account),
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Settings/Personalize/Modules/Create', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeModuleCreateViewHelper::data(Auth::user()->account),
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

        (new CreateModule())->execute($data);

        return Redirect::route('settings.personalize.module.index');
    }
}
