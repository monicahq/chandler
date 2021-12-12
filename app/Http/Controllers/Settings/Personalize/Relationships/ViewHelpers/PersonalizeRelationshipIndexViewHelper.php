<?php

namespace App\Http\Controllers\Settings\Personalize\Relationships\ViewHelpers;

use App\Models\Account;

class PersonalizeRelationshipIndexViewHelper
{
    public static function data(Account $account): array
    {
        $relationshipGroupTypes = $account->groupTypes()
            ->with('types')
            ->
        return [
            'url' => [
                'settings' => route('settings.index'),
                'personalize' => route('settings.personalize.index'),
            ],
        ];
    }
}
