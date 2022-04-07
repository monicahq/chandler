<?php

namespace App\Http\Controllers\Settings\Personalize\Modules;

use Inertia\Inertia;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Account\ManageTemplate\CreateTemplate;
use App\Services\Account\ManageTemplate\UpdateTemplate;
use App\Services\Account\ManageTemplate\DestroyTemplate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Personalize\Modules\ViewHelpers\PersonalizeModuleIndexViewHelper;
use App\Http\Controllers\Settings\Personalize\Templates\ViewHelpers\PersonalizeTemplateShowViewHelper;
use App\Http\Controllers\Settings\Personalize\Templates\ViewHelpers\PersonalizeTemplateIndexViewHelper;

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
