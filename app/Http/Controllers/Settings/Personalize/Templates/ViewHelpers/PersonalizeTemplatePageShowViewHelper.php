<?php

namespace App\Http\Controllers\Settings\Personalize\Templates\ViewHelpers;

use App\Models\Module;
use App\Models\Template;
use App\Models\TemplatePage;

class PersonalizeTemplatePageShowViewHelper
{
    public static function data(TemplatePage $templatePage): array
    {
        $modules = $templatePage->modules()
            ->orderBy('position', 'asc')
            ->get();

        $collection = collect();
        foreach ($modules as $module) {
            $collection->push(self::dtoModule($module));
        }

        return [
            'page' => [
                'id' => $templatePage->id,
                'name' => $templatePage->name,
            ],
            'modules' => $collection,
        ];
    }

    public static function dtoModule(Module $module): array
    {
        return [
            'id' => $module->id,
            'name' => $module->name,
            'position' => $module->position,
        ];
    }
}
