<?php

namespace App\Http\Controllers\Settings\Notifications;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Notifications\ViewHelpers\NotificationsIndexViewHelper;
use App\Services\User\NotificationChannels\SendTestEmail;

class NotificationsTestController extends Controller
{
    public function store(Request $request, int $userNotificationChannelId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'user_notification_channel_id' => $userNotificationChannelId,
        ];

        (new SendTestEmail)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
