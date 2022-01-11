<?php

namespace App\Http\Controllers\Settings\Personalize\Templates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Personalize\Templates\ViewHelpers\PersonalizeTemplatePageShowViewHelper;
use Illuminate\Support\Facades\Auth;
use App\Services\Account\ManageTemplate\UpdateTemplatePagePosition;
use App\Http\Controllers\Settings\Personalize\Templates\ViewHelpers\PersonalizeTemplateShowViewHelper;
use App\Models\TemplatePage;
use App\Services\Account\ManageTemplate\UpdateModulePosition;

class PersonalizeTemplatePageModulesPositionController extends Controller
{
    public function update(Request $request, int $templateId, int $templatePageId, int $moduleId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'template_id' => $templateId,
            'template_page_id' => $templatePageId,
            'module_id' => $moduleId,
            'new_position' => $request->input('position'),
        ];

        $templatePage = TemplatePage::findOrFail($templatePageId);
        $module = (new UpdateModulePosition)->execute($data);

        return response()->json([
            'data' => PersonalizeTemplatePageShowViewHelper::dtoModule($templatePage, $module),
        ], 200);
    }
}
