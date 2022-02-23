<?php

namespace App\Http\Controllers\Settings\Notifications;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Notifications\ViewHelpers\NotificationsIndexViewHelper;

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
