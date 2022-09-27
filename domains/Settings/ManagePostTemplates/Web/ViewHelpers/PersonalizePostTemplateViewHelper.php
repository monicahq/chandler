<?php

namespace App\Settings\ManagePostTemplates\Web\ViewHelpers;

use App\Models\Account;
use App\Models\PostTemplate;

class PersonalizePostTemplateViewHelper
{
    public static function data(Account $account): array
    {
        $postTemplates = $account->postTemplates()
            ->orderBy('position', 'asc')
            ->get();

        $collection = collect();
        foreach ($postTemplates as $postTemplate) {
            $collection->push(self::dto($postTemplate));
        }

        return [
            'post_templates' => $collection,
            'url' => [
                'settings' => route('settings.index'),
                'personalize' => route('settings.personalize.index'),
                'store' => route('settings.personalize.post_templates.store'),
            ],
        ];
    }

    public static function dto(PostTemplate $postTemplate): array
    {
        return [
            'id' => $postTemplate->id,
            'label' => $postTemplate->label,
            'position' => $postTemplate->position,
            'url' => [
                'position' => route('settings.personalize.post_templates.order.update', [
                    'postTemplate' => $postTemplate->id,
                ]),
                'update' => route('settings.personalize.post_templates.update', [
                    'postTemplate' => $postTemplate->id,
                ]),
                'destroy' => route('settings.personalize.post_templates.destroy', [
                    'postTemplate' => $postTemplate->id,
                ]),
            ],
        ];
    }
}
