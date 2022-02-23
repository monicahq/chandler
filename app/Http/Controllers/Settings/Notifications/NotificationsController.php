<?php

namespace App\Http\Controllers\Settings\Notifications;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Notifications\ViewHelpers\NotificationsIndexViewHelper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Preferences\ViewHelpers\PreferencesIndexViewHelper;

class NotificationsController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Notifications/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => NotificationsIndexViewHelper::data(Auth::user()),
        ]);
    }
}
