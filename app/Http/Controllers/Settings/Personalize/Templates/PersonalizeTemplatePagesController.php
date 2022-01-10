<?php

namespace App\Http\Controllers\Settings\Personalize\Templates;

use Inertia\Inertia;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Account\ManageTemplate\CreateTemplate;
use App\Services\Account\ManageTemplate\UpdateTemplate;
use App\Services\Account\ManageTemplate\DestroyTemplate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Personalize\Templates\ViewHelpers\PersonalizeTemplateShowViewHelper;
use App\Http\Controllers\Settings\Personalize\Templates\ViewHelpers\PersonalizeTemplateIndexViewHelper;
use App\Services\Account\ManageTemplate\CreateTemplatePage;
use App\Services\Account\ManageTemplate\DestroyTemplatePage;
use App\Services\Account\ManageTemplate\UpdateTemplatePage;

class PersonalizeTemplatePagesController extends Controller
{
    public function store(Request $request, int $templateId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'template_id' => $templateId,
            'name' => $request->input('name'),
        ];

        $templatePage = (new CreateTemplatePage)->execute($data);

        return response()->json([
            'data' => PersonalizeTemplateShowViewHelper::dtoTemplatePage($templatePage->template, $templatePage),
        ], 201);
    }

    public function update(Request $request, int $templateId, int $templatePageId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'template_id' => $templateId,
            'template_page_id' => $templatePageId,
            'name' => $request->input('name'),
        ];

        $templatePage = (new UpdateTemplatePage)->execute($data);

        return response()->json([
            'data' => PersonalizeTemplateShowViewHelper::dtoTemplatePage($templatePage->template, $templatePage),
        ], 200);
    }

    public function destroy(Request $request, int $templateId, int $templatePageId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'template_id' => $templateId,
            'template_page_id' => $templatePageId,
        ];

        (new DestroyTemplatePage)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
