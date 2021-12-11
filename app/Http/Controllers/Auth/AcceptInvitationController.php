<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Account\ManageUsers\InviteUser;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Users\ViewHelpers\UserIndexViewHelper;
use App\Http\Controllers\Settings\Users\ViewHelpers\UserCreateViewHelper;

class AcceptInvitationController extends Controller
{
    public function show(Request $request, string $code)
    {
        return Inertia::render('Auth/AcceptInvitation', [
        ]);
    }
}
