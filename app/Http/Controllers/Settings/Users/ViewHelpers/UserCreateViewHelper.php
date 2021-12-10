<?php

namespace App\Http\Controllers\Settings\Users\ViewHelpers;

use App\Models\Account;
use App\Helpers\DateHelper;

class UserCreateViewHelper
{
    public static function data(): array
    {
        return [
            'url' => [
                'settings' => route('settings.index'),
                'back' => route('settings.user.index'),
                'store' => route('settings.user.store'),
            ],
        ];
    }
}
