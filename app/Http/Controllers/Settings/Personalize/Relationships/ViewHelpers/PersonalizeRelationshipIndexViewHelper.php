<?php

namespace App\Http\Controllers\Settings\Personalize\Relationships\ViewHelpers;

use App\Models\Account;

class PersonalizeRelationshipIndexViewHelper
{
    public static function data(Account $account): array
    {
        $relationshipGroupTypes = $account->relationshipGroupTypes()
            ->with('types')
            ->orderBy('name', 'asc')
            ->get();

        $collection = collect();
        foreach ($relationshipGroupTypes as $relationshipGroupType) {
            $collection->push([
                'id' => $relationshipGroupType->id,
                'name' => $relationshipGroupType->name,
                'types' => $relationshipGroupType->types->map(function ($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->name,
                        'name_reverse_relationship' => $type->name_reverse_relationship,
                    ];
                }),
            ]);
        }

        return [
            'group_types' => $collection,
            'url' => [
                'settings' => route('settings.index'),
                'personalize' => route('settings.personalize.index'),
            ],
        ];
    }
}
