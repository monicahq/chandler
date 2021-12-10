<?php

namespace App\Http\Controllers\Settings\Users\ViewHelpers;

use App\Models\Account;
use App\Helpers\DateHelper;

class UserCreateViewHelper
{
    public static function data(Account $account): array
    {
        $users = $account->users;

        $userCollection = collect();
        foreach ($users as $user) {
            $userCollection->push([
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'is_account_administrator' => $user->is_account_administrator,
                'invitation_code' => $user->invitation_code ? $user->invitation_code : null,
                'invitation_accepted_at' => $user->invitation_accepted_at ? DateHelper::formatDate($user->invitation_accepted_at) : null,
                'url' => [
                    'show' => route('settings.user.show', [
                        'user' => $user,
                    ]),
                ],
            ]);
        }

        return [
            'users' => $userCollection,
            'url' => [
                'settings' => [
                    'index' => route('settings.index'),
                ],
                'users' => [
                    'store' => route('settings.user.store'),
                ],
            ],
        ];
    }
}
