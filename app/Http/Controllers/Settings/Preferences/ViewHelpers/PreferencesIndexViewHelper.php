<?php

namespace App\Http\Controllers\Settings\Preferences\ViewHelpers;

use App\Models\User;

class PreferencesIndexViewHelper
{
    public static function data(User $user): array
    {
        return [
            'name_order_choices' => [
                'first_name_last_name' => '%first_name% %last_name%',
                'last_name_first_name' => '%last_name% %first_name%',
                'first_name_last_name_surname' => '%first_name% %last_name% (%surname%)',
                'last_name_first_name_surname' => '%last_name% %first_name% (%surname%)',

            ],
            'url' => [
                'preferences' => [
                    'index' => route('settings.preferences.index'),
                ],
                'users' => [
                    'index' => route('settings.user.index'),
                ],
                'personalize' => [
                    'index' => route('settings.personalize.index'),
                ],
                'cancel' => [
                    'index' => route('settings.cancel.index'),
                ],
            ],
        ];
    }
}
