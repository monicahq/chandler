<?php

namespace App\Settings\ManageGroupTypes\Web\ViewHelpers;

use App\Models\Account;
use App\Models\GroupType;

class PersonalizeGroupTypeViewHelper
{
    public static function data(Account $account): array
    {
        $groupTypes = $account->groupTypes()
            ->orderBy('position', 'asc')
            ->get();

        $collection = collect();
        foreach ($groupTypes as $groupType) {
            $collection->push(self::dto($groupType));
        }

        return [
            'group_types' => $collection,
            'url' => [
                'settings' => route('settings.index'),
                'personalize' => route('settings.personalize.index'),
                'store' => route('settings.personalize.group_types.store'),
            ],
        ];
    }

    public static function dto(GroupType $groupType): array
    {
        return [
            'id' => $groupType->id,
            'label' => $groupType->label,
            'position' => $groupType->position,
            'url' => [
                'position' => route('settings.personalize.group_types.order.update', [
                    'giftState' => $groupType->id,
                ]),
                'update' => route('settings.personalize.group_types.update', [
                    'giftState' => $groupType->id,
                ]),
                'destroy' => route('settings.personalize.group_types.destroy', [
                    'giftState' => $groupType->id,
                ]),
            ],
        ];
    }
}
