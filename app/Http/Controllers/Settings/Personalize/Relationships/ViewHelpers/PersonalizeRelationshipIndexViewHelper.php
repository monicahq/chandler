<?php

namespace App\Http\Controllers\Settings\Personalize\Relationships\ViewHelpers;

class PersonalizeRelationshipIndexViewHelper
{
    public static function data(): array
    {
        return [
            'url' => [
                'settings' => route('settings.index'),
                'personalize' => route('settings.personalize.index'),
            ],
        ];
    }
}
