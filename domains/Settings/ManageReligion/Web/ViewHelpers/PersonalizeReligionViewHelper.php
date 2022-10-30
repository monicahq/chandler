<?php

namespace App\Settings\ManageReligion\Web\ViewHelpers;

use App\Models\Account;
use App\Models\Religion;

class PersonalizeReligionViewHelper
{
    public static function data(Account $account): array
    {
        $religions = $account->religions()
            ->orderBy('position', 'asc')
            ->get();

        $collection = collect();
        foreach ($religions as $religion) {
            $collection->push(self::dto($religion));
        }

        return [
            'religions' => $collection,
            'url' => [
                'settings' => route('settings.index'),
                'personalize' => route('settings.personalize.index'),
                'store' => route('settings.personalize.religions.store'),
            ],
        ];
    }

    public static function dto(Religion $religion): array
    {
        return [
            'id' => $religion->id,
            'name' => $religion->name,
            'position' => $religion->position,
            'url' => [
                'position' => route('settings.personalize.religions.order.update', [
                    'religion' => $religion->id,
                ]),
                'update' => route('settings.personalize.religions.update', [
                    'religion' => $religion->id,
                ]),
                'destroy' => route('settings.personalize.religions.destroy', [
                    'religion' => $religion->id,
                ]),
            ],
        ];
    }
}
