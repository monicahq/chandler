<?php

namespace App\Http\Controllers\Settings\ViewHelpers;

class SettingsIndexViewHelper
{
    /**
     * Get all the data for the view.
     *
     * @return array
     */
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
