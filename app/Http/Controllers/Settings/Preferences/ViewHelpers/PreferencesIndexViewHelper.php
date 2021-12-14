<?php

namespace App\Http\Controllers\Settings\Preferences\ViewHelpers;

use App\Helpers\NameHelper;
use App\Models\Contact;
use App\Models\User;

class PreferencesIndexViewHelper
{
    public static function data(User $user): array
    {
        $contact = new Contact([
            'first_name' => 'James',
            'last_name' => 'Bond',
            'surname' => '007',
            'middle_name' => 'W.',
            'maiden_name' => 'Muller',
        ]);

        $nameExample = NameHelper::formatContactName($user, $contact);

        return [
            'name_example' => $nameExample,
            'url' => [
                'settings' => route('settings.index'),
                'back' => route('settings.index'),
            ],
        ];
    }
}
