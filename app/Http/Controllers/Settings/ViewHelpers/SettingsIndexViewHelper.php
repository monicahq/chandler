<?php

namespace App\Http\Controllers\Settings\ViewHelpers;

use App\Models\Account;
use App\Helpers\DateHelper;

class SettingsIndexViewHelper
{
    public static function data(): array
    {
        return [
            'url' => [
                'users' => [
                    'index' => route('settings.user.index'),
                ],
            ],
        ];
    }
}
