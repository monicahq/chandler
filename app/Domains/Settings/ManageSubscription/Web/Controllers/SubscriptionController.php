<?php

namespace App\Domains\Settings\ManageSubscription\Web\Controllers;

use App\Domains\Settings\ManageSubscription\Web\ViewHelpers\SubscriptionViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        if (! config('monica.requires_subscription')) {
            return redirect()->route('settings.index');
        }

        return Inertia::render('Settings/Subscription/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => SubscriptionViewHelper::data(),
        ]);
    }
}
