<?php

namespace App\Http\Controllers\Settings\Personalize\Relationships\ViewHelpers;

use App\Models\Account;
use App\Models\RelationshipGroupType;
use Faker\Provider\ar_EG\Person;

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
            $collection->push(self::dtoGroupType($relationshipGroupType));
        }

        return [
            'group_types' => $collection,
            'url' => [
                'settings' => route('settings.index'),
                'personalize' => route('settings.personalize.index'),
                'group_type_store' => route('settings.personalize.relationship.grouptype.store'),
            ],
        ];
    }

    public static function dtoGroupType(RelationshipGroupType $groupType): array
    {
        return [
            'id' => $groupType->id,
            'name' => $groupType->name,
            'types' => $groupType->types->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'name_reverse_relationship' => $type->name_reverse_relationship,
                ];
            }),
            'url' => [
                'destroy' => route('settings.personalize.relationship.grouptype.destroy', [
                    'groupType' => $groupType->id,
                ]),
            ],
        ];
    }
}
