<?php

namespace App\Http\Controllers\Settings\Personalize\Templates\ViewHelpers;

use App\Models\Account;
use App\Models\Template;

class PersonalizeTemplateShowViewHelper
{
    public static function data(Account $account, Template $template): array
    {
        $modules = $account->modules()
            ->orderBy('name', 'asc')
            ->get();

        $templatePages = $template->pages()
            ->orderBy('name', 'asc')
            ->get();

        $collection = collect();
        foreach ($templatePages as $templatePage) {
            $collection->push([
                'id' => $templatePage->id,
                'name' => $templatePage->name,
                'modules' => $templatePage->modules()
                    ->orderBy('name', 'asc')
                    ->get(),
            ]);
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
}
