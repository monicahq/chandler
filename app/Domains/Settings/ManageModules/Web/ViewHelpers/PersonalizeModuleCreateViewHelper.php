<?php

namespace App\Domains\Settings\ManageModules\Web\ViewHelpers;

use App\Models\Account;

class PersonalizeModuleCreateViewHelper
{
    public static function data(Account $account): array
    {
        return [
            'url' => [
                'settings' => route('settings.index'),
                'personalize' => route('settings.personalize.index'),
                'modules' => route('settings.personalize.module.index'),
                'back' => route('settings.personalize.module.index'),
                'store' => route('settings.personalize.module.store'),
            ],
        ];
    }
}
