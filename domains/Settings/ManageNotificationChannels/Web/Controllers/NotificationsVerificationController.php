<?php

namespace App\Settings\ManageNotificationChannels\Web\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Settings\ManageNotificationChannels\Services\VerifyUserNotificationChannelEmailAddress;
use App\Settings\ManageNotificationChannels\Web\ViewHelpers\NotificationsIndexViewHelper as ViewHelpersNotificationsIndexViewHelper;

class NotificationsVerificationController extends Controller
{
    public function store(Request $request, int $userNotificationChannelId, string $uuid)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'user_notification_channel_id' => $userNotificationChannelId,
            'uuid' => $uuid,
        ];

        (new VerifyUserNotificationChannelEmailAddress)->execute($data);

        return Inertia::render('Settings/Notifications/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => ViewHelpersNotificationsIndexViewHelper::data(Auth::user()),
        ]);
    }
}
