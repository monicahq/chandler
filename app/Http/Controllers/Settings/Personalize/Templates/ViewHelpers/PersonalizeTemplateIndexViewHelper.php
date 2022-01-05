<?php

namespace App\Http\Controllers\Settings\Personalize\Templates\ViewHelpers;

use App\Models\Account;
use App\Models\Pronoun;
use App\Models\Template;

class PersonalizeTemplateIndexViewHelper
{
    public static function data(Account $account): array
    {
        $templates = $account->templates()
            ->orderBy('name', 'asc')
            ->get();

        $collection = collect();
        foreach ($templates as $template) {
            $collection->push(self::dtoTemplate($template));
        }

        return [
            'templates' => $collection,
            'url' => [
                'settings' => route('settings.index'),
                'personalize' => route('settings.personalize.index'),
                'template_store' => route('settings.personalize.template.store'),
            ],
        ];
    }

    public static function dtoTemplate(Template $template): array
    {
        return [
            'id' => $template->id,
            'name' => $template->name,
            'url' => [
                'update' => route('settings.personalize.template.update', [
                    'template' => $template->id,
                ]),
                'destroy' => route('settings.personalize.template.destroy', [
                    'template' => $template->id,
                ]),
            ],
        ];
    }
}
