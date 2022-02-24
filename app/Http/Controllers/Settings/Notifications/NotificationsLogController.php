<?php

namespace App\Http\Controllers\Settings\Notifications;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Notifications\Logs\ViewHelpers\NotificationsLogIndexViewHelper;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Models\UserNotificationChannel;
use Illuminate\Support\Facades\Auth;
use App\Services\User\NotificationChannels\SendTestEmail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Inertia\Inertia;

class NotificationsLogController extends Controller
{
    public function index(Request $request, int $userNotificationChannelId)
    {
        try {
            $channel = UserNotificationChannel::where('user_id', Auth::user()->id)
                ->findOrFail($userNotificationChannelId);
        } catch (ModelNotFoundException) {
            return redirect('vaults');
        }

        return Inertia::render('Settings/Notifications/Logs/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => NotificationsLogIndexViewHelper::data($channel, Auth::user()),
        ]);
    }
}
