<?php

namespace App\Http\Controllers\Settings\Personalize\Templates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Personalize\Templates\ViewHelpers\PersonalizeTemplatePageShowViewHelper;
use Illuminate\Support\Facades\Auth;
use App\Services\Account\ManageTemplate\CreateTemplatePage;
use App\Services\Account\ManageTemplate\UpdateTemplatePage;
use App\Services\Account\ManageTemplate\DestroyTemplatePage;
use App\Http\Controllers\Settings\Personalize\Templates\ViewHelpers\PersonalizeTemplateShowViewHelper;
use App\Models\Module;
use App\Models\Template;
use App\Models\TemplatePage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PersonalizeModulesController extends Controller
{

}
